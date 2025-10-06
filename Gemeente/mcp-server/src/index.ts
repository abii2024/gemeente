#!/usr/bin/env node

/**
 * Gemeente Portal MCP Server
 * Model Context Protocol server voor interactie met de Gemeente Portal
 */

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
  Tool,
} from "@modelcontextprotocol/sdk/types.js";
import axios from "axios";
import * as dotenv from "dotenv";
import { BrowserAutomation } from "./browser-automation.js";

// Laad environment variabelen
dotenv.config();

const API_BASE_URL = process.env.API_BASE_URL || "http://gemeente.test/api";
const APP_URL = process.env.APP_URL || "http://gemeente.test";

// Axios instance met basis configuratie
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

/**
 * MCP Server voor Gemeente Portal
 */
class GemeenteMCPServer {
  private server: Server;
  private browser: BrowserAutomation;

  constructor() {
    this.server = new Server(
      {
        name: "gemeente-portal-mcp",
        version: "1.0.0",
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );

    this.browser = new BrowserAutomation();
    this.setupHandlers();
    this.setupErrorHandling();
  }

  /**
   * Setup request handlers
   */
  private setupHandlers(): void {
    // List available tools
    this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
      tools: this.getTools(),
    }));

    // Handle tool calls
    this.server.setRequestHandler(CallToolRequestSchema, async (request) =>
      this.handleToolCall(request)
    );
  }

  /**
   * Get available tools
   */
  private getTools(): Tool[] {
    return [
      {
        name: "get_complaints",
        description:
          "Haal alle klachten op uit de Gemeente Portal. Ondersteunt filtering op status en prioriteit.",
        inputSchema: {
          type: "object",
          properties: {
            status: {
              type: "string",
              description:
                "Filter op status: open, in_progress, resolved, closed",
              enum: ["open", "in_progress", "resolved", "closed"],
            },
            priority: {
              type: "string",
              description: "Filter op prioriteit: low, medium, high, urgent",
              enum: ["low", "medium", "high", "urgent"],
            },
            limit: {
              type: "number",
              description: "Aantal resultaten (standaard: 10)",
              default: 10,
            },
          },
        },
      },
      {
        name: "get_complaint_by_id",
        description:
          "Haal een specifieke klacht op aan de hand van het ID. Inclusief alle details, bijlagen en notities.",
        inputSchema: {
          type: "object",
          properties: {
            id: {
              type: "number",
              description: "Het ID van de klacht",
            },
          },
          required: ["id"],
        },
      },
      {
        name: "create_complaint",
        description:
          "Maak een nieuwe klacht aan in de Gemeente Portal. Vereist titel, beschrijving en contactgegevens.",
        inputSchema: {
          type: "object",
          properties: {
            title: {
              type: "string",
              description: "Titel van de klacht",
            },
            description: {
              type: "string",
              description: "Gedetailleerde beschrijving van het probleem",
            },
            category: {
              type: "string",
              description: "Categorie van de klacht",
              enum: [
                "infrastructuur",
                "afval",
                "overlast",
                "openbare_ruimte",
                "verkeer",
                "overig",
              ],
            },
            priority: {
              type: "string",
              description: "Prioriteit niveau",
              enum: ["low", "medium", "high", "urgent"],
              default: "medium",
            },
            reporter_name: {
              type: "string",
              description: "Naam van de melder",
            },
            reporter_email: {
              type: "string",
              description: "Email van de melder",
            },
            reporter_phone: {
              type: "string",
              description: "Telefoonnummer (optioneel)",
            },
            location: {
              type: "string",
              description: "Adres of locatie van het probleem",
            },
            lat: {
              type: "number",
              description: "Latitude coördinaat (optioneel)",
            },
            lng: {
              type: "number",
              description: "Longitude coördinaat (optioneel)",
            },
          },
          required: [
            "title",
            "description",
            "category",
            "reporter_name",
            "reporter_email",
          ],
        },
      },
      {
        name: "update_complaint_status",
        description:
          "Update de status van een bestaande klacht. Alleen voor admin gebruikers.",
        inputSchema: {
          type: "object",
          properties: {
            id: {
              type: "number",
              description: "Het ID van de klacht",
            },
            status: {
              type: "string",
              description: "Nieuwe status",
              enum: ["open", "in_progress", "resolved", "closed"],
            },
          },
          required: ["id", "status"],
        },
      },
      {
        name: "add_complaint_note",
        description:
          "Voeg een interne notitie toe aan een klacht. Voor admin gebruikers.",
        inputSchema: {
          type: "object",
          properties: {
            complaint_id: {
              type: "number",
              description: "Het ID van de klacht",
            },
            note: {
              type: "string",
              description: "De notitie tekst",
            },
          },
          required: ["complaint_id", "note"],
        },
      },
      {
        name: "get_statistics",
        description:
          "Haal statistieken op over alle klachten: totaal, per status, per prioriteit, recente trends.",
        inputSchema: {
          type: "object",
          properties: {
            period: {
              type: "string",
              description: "Periode voor statistieken",
              enum: ["today", "week", "month", "year", "all"],
              default: "all",
            },
          },
        },
      },
      {
        name: "search_complaints",
        description:
          "Zoek klachten op basis van zoekterm in titel, beschrijving of locatie.",
        inputSchema: {
          type: "object",
          properties: {
            query: {
              type: "string",
              description: "Zoekterm",
            },
            limit: {
              type: "number",
              description: "Aantal resultaten (standaard: 10)",
              default: 10,
            },
          },
          required: ["query"],
        },
      },
      {
        name: "get_complaint_map_data",
        description:
          "Haal alle klachten op met GPS coördinaten voor kaartweergave.",
        inputSchema: {
          type: "object",
          properties: {
            status: {
              type: "string",
              description: "Filter op status (optioneel)",
            },
          },
        },
      },
      // Browser Automation Tools
      {
        name: "browser_goto",
        description:
          "Navigeer naar een specifieke pagina op de Gemeente Portal website. Gebruik dit om pagina's te bezoeken.",
        inputSchema: {
          type: "object",
          properties: {
            url: {
              type: "string",
              description:
                "URL of pad (bijv. '/dashboard', '/klachten', of volledige URL)",
            },
            waitUntil: {
              type: "string",
              description: "Wacht op: load, domcontentloaded, of networkidle",
              enum: ["load", "domcontentloaded", "networkidle"],
              default: "networkidle",
            },
          },
          required: ["url"],
        },
      },
      {
        name: "browser_login",
        description:
          "Log in op de Gemeente Portal. Gebruikt admin credentials of opgegeven email/password.",
        inputSchema: {
          type: "object",
          properties: {
            email: {
              type: "string",
              description: "Email (optioneel, gebruikt admin credentials)",
            },
            password: {
              type: "string",
              description: "Wachtwoord (optioneel)",
            },
          },
        },
      },
      {
        name: "browser_screenshot",
        description:
          "Maak een screenshot van de huidige pagina. Handig voor visuele verificatie.",
        inputSchema: {
          type: "object",
          properties: {
            fullPage: {
              type: "boolean",
              description: "Maak screenshot van hele pagina (niet alleen viewport)",
              default: false,
            },
            path: {
              type: "string",
              description: "Pad waar screenshot opgeslagen moet worden (optioneel)",
            },
          },
        },
      },
      {
        name: "browser_extract_text",
        description:
          "Extract tekst van de huidige pagina of van een specifiek element.",
        inputSchema: {
          type: "object",
          properties: {
            selector: {
              type: "string",
              description: "CSS selector (optioneel, extract hele pagina als leeg)",
            },
          },
        },
      },
      {
        name: "browser_extract_table",
        description:
          "Extract data uit een tabel op de pagina. Returned gestructureerde data.",
        inputSchema: {
          type: "object",
          properties: {
            tableSelector: {
              type: "string",
              description: "CSS selector voor de tabel (bijv. '.complaints-table')",
            },
          },
          required: ["tableSelector"],
        },
      },
      {
        name: "browser_fill_form",
        description:
          "Vul formulier velden in op de huidige pagina.",
        inputSchema: {
          type: "object",
          properties: {
            fields: {
              type: "object",
              description:
                "Object met selector:value paren (bijv. {'input[name=\"title\"]': 'Test', '#description': 'Details'})",
            },
          },
          required: ["fields"],
        },
      },
      {
        name: "browser_click",
        description: "Klik op een element op de pagina.",
        inputSchema: {
          type: "object",
          properties: {
            selector: {
              type: "string",
              description: "CSS selector van het element om op te klikken",
            },
            waitForNavigation: {
              type: "boolean",
              description: "Wacht op navigatie na klik",
              default: false,
            },
          },
          required: ["selector"],
        },
      },
      {
        name: "browser_submit_complaint",
        description:
          "Vul het klachten formulier in en dien het in via de browser interface. Dit simuleert een echte gebruiker.",
        inputSchema: {
          type: "object",
          properties: {
            title: {
              type: "string",
              description: "Titel van de klacht",
            },
            description: {
              type: "string",
              description: "Beschrijving van het probleem",
            },
            category: {
              type: "string",
              description: "Categorie",
            },
            location: {
              type: "string",
              description: "Locatie van het probleem",
            },
            name: {
              type: "string",
              description: "Naam van de melder",
            },
            email: {
              type: "string",
              description: "Email van de melder",
            },
            phone: {
              type: "string",
              description: "Telefoonnummer (optioneel)",
            },
          },
          required: ["title", "description", "category", "name", "email"],
        },
      },
      {
        name: "browser_get_dashboard_stats",
        description:
          "Ga naar het dashboard en haal alle statistieken op die daar worden getoond.",
        inputSchema: {
          type: "object",
          properties: {
            loginFirst: {
              type: "boolean",
              description: "Eerst inloggen als admin",
              default: true,
            },
          },
        },
      },
      {
        name: "browser_get_page_info",
        description:
          "Haal algemene informatie op over de huidige pagina: URL, titel, links, etc.",
        inputSchema: {
          type: "object",
          properties: {},
        },
      },
      {
        name: "browser_close",
        description: "Sluit de browser. Gebruik dit aan het einde van taken.",
        inputSchema: {
          type: "object",
          properties: {},
        },
      },
    ];
  }

  /**
   * Handle tool calls
   */
  private async handleToolCall(request: any): Promise<any> {
    const { name, arguments: args } = request.params;

    try {
      switch (name) {
        case "get_complaints":
          return await this.getComplaints(args);

        case "get_complaint_by_id":
          return await this.getComplaintById(args.id);

        case "create_complaint":
          return await this.createComplaint(args);

        case "update_complaint_status":
          return await this.updateComplaintStatus(args.id, args.status);

        case "add_complaint_note":
          return await this.addComplaintNote(args.complaint_id, args.note);

        case "get_statistics":
          return await this.getStatistics(args.period || "all");

        case "search_complaints":
          return await this.searchComplaints(args.query, args.limit || 10);

        case "get_complaint_map_data":
          return await this.getComplaintMapData(args.status);

        // Browser Automation Tools
        case "browser_goto":
          return await this.browserGoto(args.url, args.waitUntil);

        case "browser_login":
          return await this.browserLogin(args.email, args.password);

        case "browser_screenshot":
          return await this.browserScreenshot(args.fullPage, args.path);

        case "browser_extract_text":
          return await this.browserExtractText(args.selector);

        case "browser_extract_table":
          return await this.browserExtractTable(args.tableSelector);

        case "browser_fill_form":
          return await this.browserFillForm(args.fields);

        case "browser_click":
          return await this.browserClick(args.selector, args.waitForNavigation);

        case "browser_submit_complaint":
          return await this.browserSubmitComplaint(args);

        case "browser_get_dashboard_stats":
          return await this.browserGetDashboardStats(args.loginFirst);

        case "browser_get_page_info":
          return await this.browserGetPageInfo();

        case "browser_close":
          return await this.browserClose();

        default:
          throw new Error(`Unknown tool: ${name}`);
      }
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error: ${error.message}`,
          },
        ],
        isError: true,
      };
    }
  }

  /**
   * Get complaints with filters
   */
  private async getComplaints(filters: any) {
    try {
      const response = await api.get("/complaints", { params: filters });
      return {
        content: [
          {
            type: "text",
            text: JSON.stringify(response.data, null, 2),
          },
        ],
      };
    } catch (error: any) {
      // Fallback: gebruik direct database query via Laravel route
      const url = `${APP_URL}/api/complaints`;
      const response = await axios.get(url, { params: filters });
      return {
        content: [
          {
            type: "text",
            text: JSON.stringify(response.data, null, 2),
          },
        ],
      };
    }
  }

  /**
   * Get complaint by ID
   */
  private async getComplaintById(id: number) {
    const url = `${APP_URL}/api/complaints/${id}`;
    const response = await axios.get(url);
    return {
      content: [
        {
          type: "text",
          text: JSON.stringify(response.data, null, 2),
        },
      ],
    };
  }

  /**
   * Create new complaint
   */
  private async createComplaint(data: any) {
    const url = `${APP_URL}/api/complaints`;
    const response = await axios.post(url, data);
    return {
      content: [
        {
          type: "text",
          text: `✅ Klacht succesvol aangemaakt!\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  /**
   * Update complaint status
   */
  private async updateComplaintStatus(id: number, status: string) {
    const url = `${APP_URL}/api/complaints/${id}/status`;
    const response = await axios.patch(url, { status });
    return {
      content: [
        {
          type: "text",
          text: `✅ Status succesvol bijgewerkt naar: ${status}\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  /**
   * Add note to complaint
   */
  private async addComplaintNote(complaintId: number, note: string) {
    const url = `${APP_URL}/api/complaints/${complaintId}/notes`;
    const response = await axios.post(url, { body: note });
    return {
      content: [
        {
          type: "text",
          text: `✅ Notitie toegevoegd!\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  /**
   * Get statistics
   */
  private async getStatistics(period: string) {
    const url = `${APP_URL}/api/statistics?period=${period}`;
    const response = await axios.get(url);
    return {
      content: [
        {
          type: "text",
          text: `📊 Gemeente Portal Statistieken (${period}):\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  /**
   * Search complaints
   */
  private async searchComplaints(query: string, limit: number) {
    const url = `${APP_URL}/api/complaints/search`;
    const response = await axios.get(url, {
      params: { q: query, limit },
    });
    return {
      content: [
        {
          type: "text",
          text: `🔍 Zoekresultaten voor "${query}":\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  /**
   * Get complaint map data
   */
  private async getComplaintMapData(status?: string) {
    const url = `${APP_URL}/api/complaints/map`;
    const response = await axios.get(url, {
      params: status ? { status } : {},
    });
    return {
      content: [
        {
          type: "text",
          text: `🗺️ Klachten op de kaart:\n\n${JSON.stringify(
            response.data,
            null,
            2
          )}`,
        },
      ],
    };
  }

  // ==================== Browser Automation Methods ====================

  /**
   * Navigate to URL
   */
  private async browserGoto(
    url: string,
    waitUntil?: "load" | "domcontentloaded" | "networkidle"
  ) {
    await this.browser.goto(url, { waitUntil });
    const currentUrl = await this.browser.getCurrentUrl();
    const title = await this.browser.getTitle();

    return {
      content: [
        {
          type: "text",
          text: `🌐 Navigated to:\nURL: ${currentUrl}\nTitle: ${title}`,
        },
      ],
    };
  }

  /**
   * Login via browser
   */
  private async browserLogin(email?: string, password?: string) {
    const success = await this.browser.login(email, password);

    if (success) {
      const url = await this.browser.getCurrentUrl();
      return {
        content: [
          {
            type: "text",
            text: `✅ Succesvol ingelogd!\nHuidige URL: ${url}`,
          },
        ],
      };
    } else {
      return {
        content: [
          {
            type: "text",
            text: `❌ Login gefaald. Check credentials en probeer opnieuw.`,
          },
        ],
        isError: true,
      };
    }
  }

  /**
   * Take screenshot
   */
  private async browserScreenshot(fullPage?: boolean, path?: string) {
    const screenshot = await this.browser.screenshot({ fullPage, path });
    const base64 = screenshot.toString("base64");

    return {
      content: [
        {
          type: "text",
          text: `📸 Screenshot gemaakt${path ? ` en opgeslagen in: ${path}` : ""}\nGrootte: ${screenshot.length} bytes`,
        },
        {
          type: "text",
          text: `Data: data:image/png;base64,${base64.substring(0, 100)}...`,
        },
      ],
    };
  }

  /**
   * Extract text from page
   */
  private async browserExtractText(selector?: string) {
    const text = await this.browser.extractText(selector);
    const url = await this.browser.getCurrentUrl();

    return {
      content: [
        {
          type: "text",
          text: `📄 Extracted text from: ${url}\n${selector ? `Selector: ${selector}` : "Full page"}\n\n${text}`,
        },
      ],
    };
  }

  /**
   * Extract table data
   */
  private async browserExtractTable(tableSelector: string) {
    const data = await this.browser.extractTableData(tableSelector);
    const url = await this.browser.getCurrentUrl();

    return {
      content: [
        {
          type: "text",
          text: `📊 Table data from: ${url}\nSelector: ${tableSelector}\nRows: ${data.length}\n\n${JSON.stringify(data, null, 2)}`,
        },
      ],
    };
  }

  /**
   * Fill form
   */
  private async browserFillForm(fields: Record<string, string>) {
    await this.browser.fillForm(fields);
    const url = await this.browser.getCurrentUrl();

    return {
      content: [
        {
          type: "text",
          text: `✏️ Formulier ingevuld op: ${url}\nVelden: ${Object.keys(fields).length}`,
        },
      ],
    };
  }

  /**
   * Click element
   */
  private async browserClick(selector: string, waitForNavigation?: boolean) {
    await this.browser.click(selector, { waitForNavigation });
    const url = await this.browser.getCurrentUrl();

    return {
      content: [
        {
          type: "text",
          text: `👆 Clicked: ${selector}\nHuidige URL: ${url}`,
        },
      ],
    };
  }

  /**
   * Submit complaint via browser
   */
  private async browserSubmitComplaint(data: {
    title: string;
    description: string;
    category: string;
    location: string;
    name: string;
    email: string;
    phone?: string;
  }) {
    // Navigate to complaints page
    await this.browser.goto("/klachten/nieuw");

    // Fill form
    await this.browser.fillForm({
      'input[name="title"]': data.title,
      'textarea[name="description"]': data.description,
      'select[name="category"]': data.category,
      'input[name="location"]': data.location,
      'input[name="reporter_name"]': data.name,
      'input[name="reporter_email"]': data.email,
      ...(data.phone ? { 'input[name="reporter_phone"]': data.phone } : {}),
    });

    // Submit
    await this.browser.click('button[type="submit"]', {
      waitForNavigation: true,
    });

    const url = await this.browser.getCurrentUrl();
    const text = await this.browser.extractText(".success-message, .alert");

    return {
      content: [
        {
          type: "text",
          text: `✅ Klacht ingediend via browser!\n\nTitel: ${data.title}\nURL: ${url}\n\nResponse:\n${text}`,
        },
      ],
    };
  }

  /**
   * Get dashboard stats via browser
   */
  private async browserGetDashboardStats(loginFirst: boolean = true) {
    if (loginFirst) {
      await this.browser.login();
    }

    await this.browser.goto("/dashboard");

    // Extract statistics from dashboard
    const stats = await this.browser.extractText(".statistics, .stats-grid");
    const tableData = await this.browser
      .extractTableData(".complaints-table")
      .catch(() => []);

    return {
      content: [
        {
          type: "text",
          text: `📊 Dashboard Statistieken:\n\n${stats}\n\nRecente klachten:\n${JSON.stringify(tableData, null, 2)}`,
        },
      ],
    };
  }

  /**
   * Get page info
   */
  private async browserGetPageInfo() {
    const url = await this.browser.getCurrentUrl();
    const title = await this.browser.getTitle();
    const links = await this.browser.getAllLinks();
    const cookies = await this.browser.getCookies();

    return {
      content: [
        {
          type: "text",
          text: `ℹ️ Pagina Informatie:\n\nURL: ${url}\nTitel: ${title}\n\nLinks: ${links.length}\nCookies: ${cookies.length}\n\nTop 10 links:\n${links
            .slice(0, 10)
            .map((l) => `- ${l.text}: ${l.href}`)
            .join("\n")}`,
        },
      ],
    };
  }

  /**
   * Close browser
   */
  private async browserClose() {
    await this.browser.close();

    return {
      content: [
        {
          type: "text",
          text: `👋 Browser gesloten.`,
        },
      ],
    };
  }

  /**
   * Setup error handling
   */
  private setupErrorHandling(): void {
    this.server.onerror = (error) => {
      console.error("[MCP Error]", error);
    };

    process.on("SIGINT", async () => {
      await this.browser.close();
      await this.server.close();
      process.exit(0);
    });
  }

  /**
   * Start the server
   */
  async start(): Promise<void> {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error("🚀 Gemeente Portal MCP Server gestart!");
  }
}

// Start the server
const server = new GemeenteMCPServer();
server.start().catch((error) => {
  console.error("Failed to start server:", error);
  process.exit(1);
});
