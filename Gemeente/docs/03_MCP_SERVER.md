# 🤖 MCP Server - Complete Implementatie Uitleg

**Onderwerp:** Model Context Protocol Server met TypeScript  
**Datum:** 6 oktober 2025

---

## 📋 Inhoudsopgave

1. [Wat is MCP?](#wat-is-mcp)
2. [Server Architectuur](#server-architectuur)
3. [Code Uitleg: Main Server](#code-uitleg-main-server)
4. [Code Uitleg: Browser Automation](#code-uitleg-browser-automation)
5. [Tools Documentatie](#tools-documentatie)
6. [TypeScript Concepten](#typescript-concepten)

---

## 🎯 Wat is MCP?

**Model Context Protocol (MCP)** is een standaard protocol voor AI-agents om te communiceren met externe tools en data sources.

### Waarom MCP?

```
┌─────────────┐
│  AI Agent   │  (ChatGPT, Claude, etc.)
│  (Client)   │
└──────┬──────┘
       │ JSON-RPC via stdio
       ↓
┌─────────────┐
│ MCP Server  │  Gemeente Server (deze implementatie)
└──────┬──────┘
       │
       ├─→ API Tools (8) ──→ Laravel API ──→ Database
       │
       └─→ Browser Tools (11) ──→ Playwright ──→ Website
```

### JSON-RPC Protocol

MCP gebruikt **JSON-RPC 2.0** over **stdio** (standard input/output):

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "tools/call",
  "params": {
    "name": "get_complaints",
    "arguments": {
      "status": "open",
      "limit": 10
    }
  }
}
```

**Response:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "content": [
      {
        "type": "text",
        "text": "📋 Gevonden klachten:\n1. Kapotte straatlantaarn..."
      }
    ]
  }
}
```

---

## 🏗️ Server Architectuur

### File Structuur

```
mcp-server/
├── src/
│   ├── index.ts                    # Main MCP server (975 lines)
│   └── browser-automation.ts       # Playwright wrapper (350+ lines)
│
├── dist/                           # Compiled JavaScript
│   ├── index.js
│   └── browser-automation.js
│
├── node_modules/                   # Dependencies (44 packages)
│   ├── @modelcontextprotocol/sdk/  # MCP framework
│   ├── playwright/                 # Browser automation
│   ├── axios/                      # HTTP client
│   └── typescript/                 # TypeScript compiler
│
├── package.json                    # Dependencies & scripts
├── tsconfig.json                   # TypeScript config
├── .env                           # Environment variables
└── README.md                      # Setup instructions
```

### Class Diagram

```
┌──────────────────────────┐
│  GemeenteMCPServer       │
├──────────────────────────┤
│ - server: Server         │
│ - browser: BrowserAuto   │
│ - baseUrl: string        │
├──────────────────────────┤
│ + constructor()          │
│ + setupTools()           │
│ + setupAPITools()        │
│ + setupBrowserTools()    │
│ + run()                  │
└──────────┬───────────────┘
           │
           │ heeft
           ↓
┌──────────────────────────┐
│  BrowserAutomation       │
├──────────────────────────┤
│ - browser: Browser?      │
│ - page: Page?            │
│ - context: BrowserCtx?   │
├──────────────────────────┤
│ + launch()               │
│ + goto()                 │
│ + login()                │
│ + screenshot()           │
│ + extractText()          │
│ + fillForm()             │
│ + click()                │
│ + close()                │
└──────────────────────────┘
```

---

## 📝 Code Uitleg: Main Server

### **File: `mcp-server/src/index.ts`**

#### 1. **Imports & Setup**

```typescript
#!/usr/bin/env node

// Shebang voor executable script (chmod +x)

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from '@modelcontextprotocol/sdk/types.js';
import axios from 'axios';
import { BrowserAutomation } from './browser-automation.js';

/**
 * Environment Variables
 * Laad uit .env bestand of gebruik defaults
 */
const BASE_URL = process.env.BASE_URL || 'http://gemeente.test';
const API_URL = `${BASE_URL}/api`;
```

**Uitleg:**
- `#!/usr/bin/env node` - Maakt bestand executable als script
- `@modelcontextprotocol/sdk` - Officiële MCP SDK van Anthropic
- `StdioServerTransport` - Communicatie via stdin/stdout (JSON-RPC)
- `axios` - HTTP client voor API calls
- `BrowserAutomation` - Custom Playwright wrapper class

---

#### 2. **GemeenteMCPServer Class**

```typescript
/**
 * Main MCP Server Class
 * 
 * Verantwoordelijk voor:
 * - Tool registratie (19 tools totaal)
 * - Request handling (JSON-RPC)
 * - Response formatting
 * - Error handling
 */
class GemeenteMCPServer {
  private server: Server;              // MCP Server instance
  private browser: BrowserAutomation;  // Browser automation instance
  private baseUrl: string;             // Base URL voor website
  private apiUrl: string;              // API endpoint base

  constructor() {
    // Initialiseer MCP server met metadata
    this.server = new Server(
      {
        name: 'gemeente-mcp-server',
        version: '1.0.0',
      },
      {
        capabilities: {
          tools: {},  // Ondersteunt tools
        },
      }
    );

    this.baseUrl = BASE_URL;
    this.apiUrl = API_URL;
    this.browser = new BrowserAutomation();

    // Setup error handlers
    this.server.onerror = (error) => {
      console.error('[MCP Error]', error);
    };

    // Registreer request handlers
    this.setupHandlers();
    
    // Registreer tools
    this.setupTools();
  }
```

**Uitleg Constructor:**
- `Server` object is het hart van MCP server
- `name` en `version` identificeren de server
- `capabilities` vertelt client welke features beschikbaar zijn
- `onerror` handler vangt alle uncaught errors

---

#### 3. **Request Handlers**

```typescript
  /**
   * Setup MCP Protocol Handlers
   * 
   * Handelt twee soorten requests af:
   * 1. tools/list - Lijst van beschikbare tools
   * 2. tools/call - Uitvoeren van een tool
   */
  private setupHandlers(): void {
    // Handler: Lijst alle beschikbare tools
    this.server.setRequestHandler(
      ListToolsRequestSchema,
      async () => ({
        tools: this.getAllTools(),
      })
    );

    // Handler: Voer tool uit
    this.server.setRequestHandler(
      CallToolRequestSchema,
      async (request) => {
        const { name, arguments: args } = request.params;

        try {
          // Zoek tool handler
          const handler = this.toolHandlers.get(name);
          
          if (!handler) {
            throw new Error(`Unknown tool: ${name}`);
          }

          // Voer tool uit met arguments
          const result = await handler(args || {});

          // Return formatted response
          return {
            content: [
              {
                type: 'text',
                text: result,
              },
            ],
          };
        } catch (error) {
          // Error handling
          const errorMessage = error instanceof Error 
            ? error.message 
            : 'Unknown error';
          
          return {
            content: [
              {
                type: 'text',
                text: `❌ Error: ${errorMessage}`,
              },
            ],
            isError: true,
          };
        }
      }
    );
  }
```

**JSON-RPC Flow:**
```
Client Request → ListToolsRequestSchema → getAllTools() → Response
Client Request → CallToolRequestSchema → toolHandlers.get() → Execute → Response
```

---

#### 4. **Tool Registration**

```typescript
  /**
   * Tool Handlers Map
   * Key: tool name
   * Value: async function (args) => result
   */
  private toolHandlers = new Map<string, (args: any) => Promise<string>>();

  /**
   * Registreer alle tools
   */
  private setupTools(): void {
    this.setupAPITools();      // 8 API tools
    this.setupBrowserTools();  // 11 browser tools
  }

  /**
   * Registreer API Tools (8 stuks)
   */
  private setupAPITools(): void {
    // Tool 1: Get Complaints
    this.toolHandlers.set('get_complaints', async (args) => {
      const { status, priority, category, limit = 50 } = args;
      
      // Build query parameters
      const params = new URLSearchParams();
      if (status) params.append('status', status);
      if (priority) params.append('priority', priority);
      if (category) params.append('category', category);
      params.append('limit', limit.toString());

      // API call met axios
      const response = await axios.get(
        `${this.apiUrl}/complaints?${params.toString()}`
      );

      // Format response als string
      const complaints = response.data.data;
      
      if (complaints.length === 0) {
        return '📋 Geen klachten gevonden met deze filters.';
      }

      // Format elke klacht als leesbare tekst
      let result = `📋 Gevonden klachten (${complaints.length}):\n\n`;
      
      complaints.forEach((c: any, index: number) => {
        result += `${index + 1}. **${c.title}**\n`;
        result += `   Status: ${c.status} | Prioriteit: ${c.priority}\n`;
        result += `   Categorie: ${c.category}\n`;
        result += `   Locatie: ${c.address}\n`;
        result += `   Aangemaakt: ${c.created_at}\n`;
        result += `   ID: ${c.id}\n\n`;
      });

      return result;
    });

    // Tool 2: Get Complaint by ID
    this.toolHandlers.set('get_complaint_by_id', async (args) => {
      const { id } = args;
      
      if (!id) {
        throw new Error('Parameter "id" is verplicht');
      }

      const response = await axios.get(`${this.apiUrl}/complaints/${id}`);
      const complaint = response.data.data;

      // Format detailed view
      let result = `📋 Klacht Details #${complaint.id}\n\n`;
      result += `**${complaint.title}**\n\n`;
      result += `Beschrijving:\n${complaint.description}\n\n`;
      result += `Status: ${complaint.status}\n`;
      result += `Prioriteit: ${complaint.priority}\n`;
      result += `Categorie: ${complaint.category}\n`;
      result += `Locatie: ${complaint.address}\n`;
      
      if (complaint.latitude && complaint.longitude) {
        result += `GPS: ${complaint.latitude}, ${complaint.longitude}\n`;
      }
      
      result += `\nContact:\n`;
      result += `Naam: ${complaint.contact_name}\n`;
      result += `Email: ${complaint.contact_email}\n`;
      
      if (complaint.contact_phone) {
        result += `Telefoon: ${complaint.contact_phone}\n`;
      }
      
      result += `\nAangemaakt: ${complaint.created_at}\n`;
      result += `Laatst gewijzigd: ${complaint.updated_at}\n`;

      // Voeg notes toe als die er zijn
      if (complaint.notes && complaint.notes.length > 0) {
        result += `\n📝 Notities (${complaint.notes.length}):\n`;
        complaint.notes.forEach((note: any, i: number) => {
          result += `${i + 1}. ${note.note}\n`;
          result += `   Door: ${note.user?.name || 'Onbekend'}\n`;
          result += `   Op: ${note.created_at}\n\n`;
        });
      }

      return result;
    });

    // Tool 3: Create Complaint
    this.toolHandlers.set('create_complaint', async (args) => {
      const {
        title,
        description,
        category,
        address,
        latitude,
        longitude,
        contact_name,
        contact_email,
        contact_phone,
      } = args;

      // Validatie
      if (!title || !description || !category || !address) {
        throw new Error('Verplichte velden: title, description, category, address');
      }

      if (!contact_name || !contact_email) {
        throw new Error('Verplichte contactgegevens: contact_name, contact_email');
      }

      // POST request
      const response = await axios.post(`${this.apiUrl}/complaints`, {
        title,
        description,
        category,
        address,
        latitude: latitude || null,
        longitude: longitude || null,
        contact_name,
        contact_email,
        contact_phone: contact_phone || null,
      });

      const complaint = response.data.data;

      return `✅ Klacht succesvol aangemaakt!\n\n` +
             `ID: ${complaint.id}\n` +
             `Titel: ${complaint.title}\n` +
             `Status: ${complaint.status}\n` +
             `Aangemaakt op: ${complaint.created_at}`;
    });

    // Tool 4: Update Complaint Status
    this.toolHandlers.set('update_complaint_status', async (args) => {
      const { id, status } = args;

      if (!id || !status) {
        throw new Error('Parameters "id" en "status" zijn verplicht');
      }

      const validStatuses = ['open', 'in_behandeling', 'afgerond', 'gesloten'];
      if (!validStatuses.includes(status)) {
        throw new Error(`Status moet één van zijn: ${validStatuses.join(', ')}`);
      }

      const response = await axios.patch(
        `${this.apiUrl}/complaints/${id}/status`,
        { status }
      );

      const complaint = response.data.data;

      return `✅ Status bijgewerkt!\n\n` +
             `Klacht ID: ${complaint.id}\n` +
             `Nieuwe status: ${complaint.status}\n` +
             `Bijgewerkt op: ${complaint.updated_at}`;
    });

    // Tool 5: Add Complaint Note
    this.toolHandlers.set('add_complaint_note', async (args) => {
      const { id, note, is_internal = false } = args;

      if (!id || !note) {
        throw new Error('Parameters "id" en "note" zijn verplicht');
      }

      const response = await axios.post(
        `${this.apiUrl}/complaints/${id}/notes`,
        { note, is_internal }
      );

      const newNote = response.data.data;

      return `✅ Notitie toegevoegd!\n\n` +
             `Klacht ID: ${id}\n` +
             `Notitie: ${newNote.note}\n` +
             `Type: ${newNote.is_internal ? 'Intern' : 'Publiek'}\n` +
             `Toegevoegd op: ${newNote.created_at}`;
    });

    // Tool 6: Get Statistics
    this.toolHandlers.set('get_statistics', async (args) => {
      const { period = 'all' } = args;

      const response = await axios.get(
        `${this.apiUrl}/statistics?period=${period}`
      );

      const stats = response.data.data;

      let result = `📊 Gemeente Statistieken (${period})\n\n`;
      
      result += `**Totalen:**\n`;
      result += `Totaal: ${stats.totals.all}\n`;
      result += `Open: ${stats.totals.open}\n`;
      result += `In behandeling: ${stats.totals.in_behandeling}\n`;
      result += `Afgerond: ${stats.totals.afgerond}\n`;
      result += `Gesloten: ${stats.totals.gesloten}\n\n`;

      result += `**Per Prioriteit:**\n`;
      Object.entries(stats.by_priority || {}).forEach(([prio, count]) => {
        result += `${prio}: ${count}\n`;
      });

      result += `\n**Top Categorieën:**\n`;
      (stats.top_categories || []).forEach((cat: any, i: number) => {
        result += `${i + 1}. ${cat.category}: ${cat.count}\n`;
      });

      if (stats.avg_resolution_days) {
        result += `\n⏱️ Gemiddelde afhandeltijd: ${stats.avg_resolution_days} dagen\n`;
      }

      return result;
    });

    // Tool 7: Search Complaints
    this.toolHandlers.set('search_complaints', async (args) => {
      const { query, limit = 20 } = args;

      if (!query || query.length < 3) {
        throw new Error('Zoekterm moet minimaal 3 karakters bevatten');
      }

      const response = await axios.get(
        `${this.apiUrl}/complaints/search?q=${encodeURIComponent(query)}&limit=${limit}`
      );

      const complaints = response.data.data;

      if (complaints.length === 0) {
        return `🔍 Geen resultaten gevonden voor "${query}"`;
      }

      let result = `🔍 Zoekresultaten voor "${query}" (${complaints.length}):\n\n`;
      
      complaints.forEach((c: any, i: number) => {
        result += `${i + 1}. ${c.title}\n`;
        result += `   ID: ${c.id} | Status: ${c.status}\n`;
        result += `   ${c.address}\n\n`;
      });

      return result;
    });

    // Tool 8: Get Map Data
    this.toolHandlers.set('get_complaint_map_data', async (args) => {
      const { status } = args;

      const params = new URLSearchParams();
      if (status) params.append('status', status);

      const response = await axios.get(
        `${this.apiUrl}/complaints/map?${params.toString()}`
      );

      const complaints = response.data.data;

      if (complaints.length === 0) {
        return '🗺️ Geen klachten met GPS coördinaten gevonden.';
      }

      let result = `🗺️ Klachten op kaart (${complaints.length}):\n\n`;
      
      complaints.forEach((c: any, i: number) => {
        result += `${i + 1}. ${c.title}\n`;
        result += `   📍 ${c.address}\n`;
        result += `   GPS: ${c.latitude}, ${c.longitude}\n`;
        result += `   Status: ${c.status} | Prioriteit: ${c.priority}\n\n`;
      });

      return result;
    });
  }
```

**Key Concepts:**

**1. Map als Handler Registry:**
```typescript
// Registreer tool
this.toolHandlers.set('tool_name', async (args) => {
  // Implementation
  return result;
});

// Roep tool aan
const handler = this.toolHandlers.get('tool_name');
const result = await handler(args);
```

**2. Axios HTTP Requests:**
```typescript
// GET request
const response = await axios.get(`${url}?param=value`);
const data = response.data;

// POST request
const response = await axios.post(url, { key: 'value' });

// PATCH request
const response = await axios.patch(url, { key: 'value' });
```

**3. Error Handling:**
```typescript
try {
  const response = await axios.get(url);
  return response.data;
} catch (error) {
  if (axios.isAxiosError(error)) {
    // HTTP error (4xx, 5xx)
    throw new Error(`API Error: ${error.response?.status}`);
  }
  throw error;
}
```

---

#### 5. **Browser Tools**

```typescript
  /**
   * Registreer Browser Automation Tools (11 stuks)
   */
  private setupBrowserTools(): void {
    // Tool 1: Browser Goto
    this.toolHandlers.set('browser_goto', async (args) => {
      const { url, wait_until = 'networkidle' } = args;

      if (!url) {
        throw new Error('Parameter "url" is verplicht');
      }

      await this.browser.launch();
      await this.browser.goto(url, wait_until);

      return `✅ Navigated naar: ${url}`;
    });

    // Tool 2: Browser Login
    this.toolHandlers.set('browser_login', async (args) => {
      const { 
        email = 'admin@gemeente.nl', 
        password = 'password' 
      } = args;

      await this.browser.launch();
      const success = await this.browser.login(email, password);

      if (success) {
        return `✅ Login succesvol als ${email}`;
      } else {
        throw new Error('Login gefaald - check credentials');
      }
    });

    // Tool 3: Browser Screenshot
    this.toolHandlers.set('browser_screenshot', async (args) => {
      const { path = 'screenshot.png', fullPage = false } = args;

      const screenshotPath = await this.browser.screenshot(path, fullPage);

      return `📸 Screenshot opgeslagen: ${screenshotPath}`;
    });

    // Tool 4: Extract Text
    this.toolHandlers.set('browser_extract_text', async (args) => {
      const { selector } = args;

      if (!selector) {
        throw new Error('Parameter "selector" is verplicht');
      }

      const text = await this.browser.extractText(selector);

      return `📝 Extracted text:\n${text}`;
    });

    // Tool 5: Extract Table Data
    this.toolHandlers.set('browser_extract_table', async (args) => {
      const { selector = 'table' } = args;

      const tableData = await this.browser.extractTableData(selector);

      let result = `📊 Table data (${tableData.length} rows):\n\n`;
      
      tableData.forEach((row, i) => {
        result += `Row ${i + 1}: ${JSON.stringify(row)}\n`;
      });

      return result;
    });

    // Tool 6: Fill Form
    this.toolHandlers.set('browser_fill_form', async (args) => {
      const { fields } = args;

      if (!fields || typeof fields !== 'object') {
        throw new Error('Parameter "fields" moet een object zijn');
      }

      await this.browser.fillForm(fields);

      return `✅ Form gevuld met ${Object.keys(fields).length} velden`;
    });

    // Tool 7: Click Element
    this.toolHandlers.set('browser_click', async (args) => {
      const { selector } = args;

      if (!selector) {
        throw new Error('Parameter "selector" is verplicht');
      }

      await this.browser.click(selector);

      return `✅ Clicked op: ${selector}`;
    });

    // Tool 8: Submit Complaint (End-to-End)
    this.toolHandlers.set('browser_submit_complaint', async (args) => {
      const complaintData = args;

      await this.browser.launch();
      await this.browser.goto(`${this.baseUrl}/klacht-indienen`);
      
      // Vul formulier
      await this.browser.fillForm({
        '#title': complaintData.title,
        '#description': complaintData.description,
        '#category': complaintData.category,
        '#address': complaintData.address,
        '#contact_name': complaintData.contact_name,
        '#contact_email': complaintData.contact_email,
      });

      // Submit
      await this.browser.click('button[type="submit"]');
      await this.browser.page!.waitForNavigation();

      return `✅ Klacht ingediend via browser!\nTitel: ${complaintData.title}`;
    });

    // Tool 9: Get Dashboard Stats
    this.toolHandlers.set('browser_get_dashboard_stats', async (args) => {
      await this.browser.launch();
      await this.browser.login();
      await this.browser.goto(`${this.baseUrl}/admin/dashboard`);

      // Extract statistieken van dashboard
      const stats = {
        total: await this.browser.extractText('.stat-total'),
        open: await this.browser.extractText('.stat-open'),
        in_behandeling: await this.browser.extractText('.stat-in-behandeling'),
        afgerond: await this.browser.extractText('.stat-afgerond'),
      };

      let result = `📊 Live Dashboard Statistieken:\n\n`;
      Object.entries(stats).forEach(([key, value]) => {
        result += `${key}: ${value}\n`;
      });

      return result;
    });

    // Tool 10: Get Page Info
    this.toolHandlers.set('browser_get_page_info', async () => {
      const info = await this.browser.getPageInfo();

      return `🌐 Page Info:\n` +
             `URL: ${info.url}\n` +
             `Title: ${info.title}\n` +
             `Meta Description: ${info.metaDescription || 'N/A'}`;
    });

    // Tool 11: Close Browser
    this.toolHandlers.set('browser_close', async () => {
      await this.browser.close();
      return `✅ Browser gesloten`;
    });
  }
```

---

#### 6. **Server Start**

```typescript
  /**
   * Get alle tools voor ListToolsRequest
   */
  private getAllTools() {
    return [
      // API Tools
      {
        name: 'get_complaints',
        description: 'Haal lijst van klachten op met optionele filters',
        inputSchema: {
          type: 'object',
          properties: {
            status: {
              type: 'string',
              description: 'Filter op status (open, in_behandeling, afgerond, gesloten)',
              enum: ['open', 'in_behandeling', 'afgerond', 'gesloten'],
            },
            priority: {
              type: 'string',
              description: 'Filter op prioriteit',
              enum: ['laag', 'normaal', 'hoog', 'urgent'],
            },
            category: {
              type: 'string',
              description: 'Filter op categorie',
            },
            limit: {
              type: 'number',
              description: 'Maximaal aantal resultaten (default: 50)',
              default: 50,
            },
          },
        },
      },
      // ... andere 18 tools
    ];
  }

  /**
   * Start de MCP server
   */
  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    
    console.error('Gemeente MCP Server running on stdio');
    console.error(`API URL: ${this.apiUrl}`);
    console.error(`Browser URL: ${this.baseUrl}`);
  }
}

// Start server
const server = new GemeenteMCPServer();
server.run().catch(console.error);
```

**Uitleg:**
- `getAllTools()` return JSON schema voor elke tool
- `inputSchema` definieert parameters met types en beschrijvingen
- `StdioServerTransport` luistert naar stdin en schrijft naar stdout
- `console.error()` voor logging (stdout is voor JSON-RPC)

---

## 🌐 Code Uitleg: Browser Automation

### **File: `mcp-server/src/browser-automation.ts`**

```typescript
import { chromium, Browser, Page, BrowserContext } from 'playwright';

/**
 * Browser Automation Class
 * 
 * Wrapper around Playwright voor browser automation
 * 
 * Features:
 * - Headless/Headful mode
 * - Cookie persistence
 * - Screenshot capture
 * - Element extraction
 * - Form automation
 */
export class BrowserAutomation {
  private browser: Browser | null = null;
  private context: BrowserContext | null = null;
  private page: Page | null = null;
  private baseUrl: string;

  constructor(baseUrl: string = 'http://gemeente.test') {
    this.baseUrl = baseUrl;
  }

  /**
   * Launch browser instance
   * 
   * @param headless - Run browser in headless mode (default: true)
   */
  async launch(headless: boolean = true): Promise<void> {
    if (this.browser) {
      return; // Already launched
    }

    // Start Chromium browser
    this.browser = await chromium.launch({
      headless,
      args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    // Create browser context (isolated session)
    this.context = await this.browser.newContext({
      viewport: { width: 1920, height: 1080 },
      userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
    });

    // Create new page
    this.page = await this.context.newPage();
  }

  /**
   * Navigate naar URL
   * 
   * @param url - URL to navigate to
   * @param waitUntil - Wait condition ('load' | 'domcontentloaded' | 'networkidle')
   */
  async goto(url: string, waitUntil: 'load' | 'domcontentloaded' | 'networkidle' = 'load'): Promise<void> {
    if (!this.page) {
      throw new Error('Browser not launched. Call launch() first.');
    }

    await this.page.goto(url, { waitUntil });
  }

  /**
   * Auto-login op admin dashboard
   * 
   * @param email - User email
   * @param password - User password
   * @returns true als login succesvol
   */
  async login(email: string = 'admin@gemeente.nl', password: string = 'password'): Promise<boolean> {
    if (!this.page) throw new Error('Browser not launched');

    // Navigate naar login page
    await this.goto(`${this.baseUrl}/login`);

    // Vul login form
    await this.page.fill('input[name="email"]', email);
    await this.page.fill('input[name="password"]', password);

    // Click submit button
    await this.page.click('button[type="submit"]');

    // Wait for navigation
    await this.page.waitForNavigation({ waitUntil: 'networkidle' });

    // Check of login succesvol (URL bevat /admin)
    const currentUrl = this.page.url();
    return currentUrl.includes('/admin');
  }

  /**
   * Screenshot maken
   * 
   * @param path - Output path
   * @param fullPage - Capture hele pagina (default: false)
   * @returns Path naar screenshot
   */
  async screenshot(path: string, fullPage: boolean = false): Promise<string> {
    if (!this.page) throw new Error('Browser not launched');

    await this.page.screenshot({ path, fullPage });
    return path;
  }

  /**
   * Extract text van element
   * 
   * @param selector - CSS selector
   * @returns Text content
   */
  async extractText(selector: string): Promise<string> {
    if (!this.page) throw new Error('Browser not launched');

    const element = await this.page.$(selector);
    if (!element) {
      throw new Error(`Element niet gevonden: ${selector}`);
    }

    const text = await element.textContent();
    return text?.trim() || '';
  }

  /**
   * Extract table data
   * 
   * @param selector - Table selector
   * @returns Array van row objects
   */
  async extractTableData(selector: string = 'table'): Promise<any[]> {
    if (!this.page) throw new Error('Browser not launched');

    return await this.page.evaluate((sel) => {
      const table = document.querySelector(sel) as HTMLTableElement;
      if (!table) return [];

      const rows = Array.from(table.querySelectorAll('tr'));
      const headers = Array.from(rows[0]?.querySelectorAll('th, td') || [])
        .map(th => th.textContent?.trim() || '');

      return rows.slice(1).map(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        const rowData: any = {};
        
        cells.forEach((cell, i) => {
          const header = headers[i] || `column_${i}`;
          rowData[header] = cell.textContent?.trim() || '';
        });
        
        return rowData;
      });
    }, selector);
  }

  /**
   * Vul formulier velden
   * 
   * @param fields - Object met selector: value pairs
   */
  async fillForm(fields: Record<string, string>): Promise<void> {
    if (!this.page) throw new Error('Browser not launched');

    for (const [selector, value] of Object.entries(fields)) {
      await this.page.fill(selector, value);
    }
  }

  /**
   * Click op element
   * 
   * @param selector - CSS selector
   */
  async click(selector: string): Promise<void> {
    if (!this.page) throw new Error('Browser not launched');
    await this.page.click(selector);
  }

  /**
   * Scroll naar element
   * 
   * @param selector - CSS selector
   */
  async scrollToElement(selector: string): Promise<void> {
    if (!this.page) throw new Error('Browser not launched');
    
    await this.page.evaluate((sel) => {
      const element = document.querySelector(sel);
      element?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, selector);
  }

  /**
   * Wait for selector
   * 
   * @param selector - CSS selector
   * @param timeout - Timeout in milliseconds
   */
  async waitForSelector(selector: string, timeout: number = 30000): Promise<void> {
    if (!this.page) throw new Error('Browser not launched');
    await this.page.waitForSelector(selector, { timeout });
  }

  /**
   * Get page info (title, URL, meta)
   */
  async getPageInfo(): Promise<{ title: string; url: string; metaDescription: string | null }> {
    if (!this.page) throw new Error('Browser not launched');

    const title = await this.page.title();
    const url = this.page.url();
    const metaDescription = await this.page.$eval(
      'meta[name="description"]',
      (el) => el.getAttribute('content')
    ).catch(() => null);

    return { title, url, metaDescription };
  }

  /**
   * Close browser en cleanup resources
   */
  async close(): Promise<void> {
    if (this.page) {
      await this.page.close();
      this.page = null;
    }
    
    if (this.context) {
      await this.context.close();
      this.context = null;
    }
    
    if (this.browser) {
      await this.browser.close();
      this.browser = null;
    }
  }
}
```

**Playwright Key Concepts:**

**1. Browser → Context → Page Hierarchy:**
```typescript
const browser = await chromium.launch();     // Browser instance
const context = await browser.newContext();  // Isolated session
const page = await context.newPage();        // Tab/Page
```

**2. Selectors:**
```typescript
// CSS selector
await page.click('#submit-button');
await page.fill('input[name="email"]', 'test@example.com');

// Text selector
await page.click('text=Submit');

// Xpath
await page.click('xpath=//button[@type="submit"]');
```

**3. Waiting Strategies:**
```typescript
// Wait for navigation
await page.waitForNavigation();

// Wait for selector
await page.waitForSelector('.success-message');

// Wait for load state
await page.waitForLoadState('networkidle');

// Custom wait
await page.waitForTimeout(5000); // 5 seconds
```

**4. Page Evaluation (run code in browser context):**
```typescript
const result = await page.evaluate(() => {
  // This code runs IN the browser
  return document.title;
});
```

---

## 🔧 TypeScript Concepten

### 1. **Type Annotations**

```typescript
// Variable types
let name: string = 'John';
let age: number = 30;
let isActive: boolean = true;
let items: string[] = ['a', 'b', 'c'];

// Function types
function greet(name: string): string {
  return `Hello, ${name}`;
}

// Object types
interface User {
  id: number;
  name: string;
  email?: string;  // Optional property
}

// Union types
let value: string | number = 'hello';
value = 42; // OK

// Generics
function identity<T>(arg: T): T {
  return arg;
}
```

### 2. **Async/Await**

```typescript
// Async function returns Promise
async function fetchData(): Promise<string> {
  const response = await axios.get('https://api.example.com');
  return response.data;
}

// Error handling
async function safeF
