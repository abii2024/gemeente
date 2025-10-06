import { chromium, Browser, Page, BrowserContext } from 'playwright';
import * as dotenv from 'dotenv';

dotenv.config();

export class BrowserAutomation {
  private browser: Browser | null = null;
  private context: BrowserContext | null = null;
  private page: Page | null = null;
  private baseUrl: string;

  constructor() {
    this.baseUrl = process.env.APP_URL || 'http://gemeente.test';
  }

  /**
   * Launch browser and create new page
   */
  async launch(headless: boolean = true): Promise<void> {
    if (this.browser) {
      return; // Already launched
    }

    this.browser = await chromium.launch({
      headless,
      args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    this.context = await this.browser.newContext({
      viewport: { width: 1920, height: 1080 },
      userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
    });

    this.page = await this.context.newPage();
  }

  /**
   * Close browser
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

  /**
   * Get current page
   */
  async getPage(): Promise<Page> {
    if (!this.page) {
      await this.launch();
    }
    return this.page!;
  }

  /**
   * Navigate to a URL
   */
  async goto(url: string, options?: { waitUntil?: 'load' | 'domcontentloaded' | 'networkidle' }): Promise<string> {
    const page = await this.getPage();
    const fullUrl = url.startsWith('http') ? url : `${this.baseUrl}${url}`;

    await page.goto(fullUrl, {
      waitUntil: options?.waitUntil || 'networkidle',
      timeout: 30000
    });

    return page.url();
  }

  /**
   * Login to the application
   */
  async login(email?: string, password?: string): Promise<boolean> {
    const page = await this.getPage();
    const loginEmail = email || process.env.ADMIN_EMAIL || 'admin@gemeente.nl';
    const loginPassword = password || process.env.ADMIN_PASSWORD || 'admin123';

    try {
      // Go to login page
      await page.goto(`${this.baseUrl}/login`);

      // Fill in credentials
      await page.fill('input[name="email"]', loginEmail);
      await page.fill('input[name="password"]', loginPassword);

      // Submit form
      await page.click('button[type="submit"]');

      // Wait for navigation
      await page.waitForURL('**/dashboard', { timeout: 10000 });

      return true;
    } catch (error) {
      console.error('Login failed:', error);
      return false;
    }
  }

  /**
   * Take a screenshot
   */
  async screenshot(options?: { fullPage?: boolean; path?: string }): Promise<Buffer> {
    const page = await this.getPage();

    return await page.screenshot({
      fullPage: options?.fullPage || false,
      path: options?.path,
      type: 'png'
    });
  }

  /**
   * Extract text content from page
   */
  async extractText(selector?: string): Promise<string> {
    const page = await this.getPage();

    if (selector) {
      const element = await page.$(selector);
      if (element) {
        return await element.textContent() || '';
      }
      return '';
    }

    return await page.textContent('body') || '';
  }

  /**
   * Extract data from table
   */
  async extractTableData(tableSelector: string): Promise<any[]> {
    const page = await this.getPage();

    return await page.evaluate((selector) => {
      const table = document.querySelector(selector);
      if (!table) return [];

      const headers: string[] = [];
      const headerCells = table.querySelectorAll('thead th');
      headerCells.forEach(cell => headers.push(cell.textContent?.trim() || ''));

      const rows: any[] = [];
      const bodyRows = table.querySelectorAll('tbody tr');

      bodyRows.forEach(row => {
        const rowData: any = {};
        const cells = row.querySelectorAll('td');

        cells.forEach((cell, index) => {
          const header = headers[index] || `column_${index}`;
          rowData[header] = cell.textContent?.trim() || '';
        });

        rows.push(rowData);
      });

      return rows;
    }, tableSelector);
  }

  /**
   * Fill form fields
   */
  async fillForm(fields: Record<string, string>): Promise<void> {
    const page = await this.getPage();

    for (const [selector, value] of Object.entries(fields)) {
      await page.fill(selector, value);
    }
  }

  /**
   * Click element
   */
  async click(selector: string, options?: { waitForNavigation?: boolean }): Promise<void> {
    const page = await this.getPage();

    if (options?.waitForNavigation) {
      await Promise.all([
        page.waitForNavigation(),
        page.click(selector)
      ]);
    } else {
      await page.click(selector);
    }
  }

  /**
   * Wait for selector
   */
  async waitForSelector(selector: string, options?: { timeout?: number; state?: 'attached' | 'detached' | 'visible' | 'hidden' }): Promise<void> {
    const page = await this.getPage();
    await page.waitForSelector(selector, {
      timeout: options?.timeout || 30000,
      state: options?.state || 'visible'
    });
  }

  /**
   * Get element attribute
   */
  async getAttribute(selector: string, attribute: string): Promise<string | null> {
    const page = await this.getPage();
    return await page.getAttribute(selector, attribute);
  }

  /**
   * Check if element exists
   */
  async elementExists(selector: string): Promise<boolean> {
    const page = await this.getPage();
    const element = await page.$(selector);
    return element !== null;
  }

  /**
   * Get page title
   */
  async getTitle(): Promise<string> {
    const page = await this.getPage();
    return await page.title();
  }

  /**
   * Get current URL
   */
  async getCurrentUrl(): Promise<string> {
    const page = await this.getPage();
    return page.url();
  }

  /**
   * Execute JavaScript on page
   */
  async evaluateScript<T>(script: string): Promise<T> {
    const page = await this.getPage();
    return await page.evaluate(script);
  }

  /**
   * Upload file
   */
  async uploadFile(selector: string, filePath: string): Promise<void> {
    const page = await this.getPage();
    await page.setInputFiles(selector, filePath);
  }

  /**
   * Select option from dropdown
   */
  async selectOption(selector: string, value: string): Promise<void> {
    const page = await this.getPage();
    await page.selectOption(selector, value);
  }

  /**
   * Get page HTML
   */
  async getPageHtml(): Promise<string> {
    const page = await this.getPage();
    return await page.content();
  }

  /**
   * Wait for a specific time
   */
  async wait(milliseconds: number): Promise<void> {
    await new Promise(resolve => setTimeout(resolve, milliseconds));
  }

  /**
   * Hover over element
   */
  async hover(selector: string): Promise<void> {
    const page = await this.getPage();
    await page.hover(selector);
  }

  /**
   * Get all links from page
   */
  async getAllLinks(): Promise<Array<{ text: string; href: string }>> {
    const page = await this.getPage();

    return await page.evaluate(() => {
      const links = Array.from(document.querySelectorAll('a'));
      return links.map(link => ({
        text: link.textContent?.trim() || '',
        href: link.href
      }));
    });
  }

  /**
   * Get form data
   */
  async getFormData(formSelector: string): Promise<Record<string, string>> {
    const page = await this.getPage();

    return await page.evaluate((selector) => {
      const form = document.querySelector(selector) as HTMLFormElement;
      if (!form) return {};

      const formData = new FormData(form);
      const data: Record<string, string> = {};

      formData.forEach((value, key) => {
        data[key] = value.toString();
      });

      return data;
    }, formSelector);
  }

  /**
   * Check checkbox or radio button
   */
  async check(selector: string): Promise<void> {
    const page = await this.getPage();
    await page.check(selector);
  }

  /**
   * Uncheck checkbox
   */
  async uncheck(selector: string): Promise<void> {
    const page = await this.getPage();
    await page.uncheck(selector);
  }

  /**
   * Get element count
   */
  async count(selector: string): Promise<number> {
    const page = await this.getPage();
    const elements = await page.$$(selector);
    return elements.length;
  }

  /**
   * Scroll to element
   */
  async scrollTo(selector: string): Promise<void> {
    const page = await this.getPage();
    await page.locator(selector).scrollIntoViewIfNeeded();
  }

  /**
   * Get cookies
   */
  async getCookies(): Promise<any[]> {
    if (!this.context) {
      await this.launch();
    }
    return await this.context!.cookies();
  }

  /**
   * Set cookies
   */
  async setCookies(cookies: any[]): Promise<void> {
    if (!this.context) {
      await this.launch();
    }
    await this.context!.addCookies(cookies);
  }

  /**
   * Clear cookies
   */
  async clearCookies(): Promise<void> {
    if (!this.context) {
      return;
    }
    await this.context.clearCookies();
  }
}
