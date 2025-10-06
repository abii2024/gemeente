import { Page } from 'playwright';
export declare class BrowserAutomation {
    private browser;
    private context;
    private page;
    private baseUrl;
    constructor();
    /**
     * Launch browser and create new page
     */
    launch(headless?: boolean): Promise<void>;
    /**
     * Close browser
     */
    close(): Promise<void>;
    /**
     * Get current page
     */
    getPage(): Promise<Page>;
    /**
     * Navigate to a URL
     */
    goto(url: string, options?: {
        waitUntil?: 'load' | 'domcontentloaded' | 'networkidle';
    }): Promise<string>;
    /**
     * Login to the application
     */
    login(email?: string, password?: string): Promise<boolean>;
    /**
     * Take a screenshot
     */
    screenshot(options?: {
        fullPage?: boolean;
        path?: string;
    }): Promise<Buffer>;
    /**
     * Extract text content from page
     */
    extractText(selector?: string): Promise<string>;
    /**
     * Extract data from table
     */
    extractTableData(tableSelector: string): Promise<any[]>;
    /**
     * Fill form fields
     */
    fillForm(fields: Record<string, string>): Promise<void>;
    /**
     * Click element
     */
    click(selector: string, options?: {
        waitForNavigation?: boolean;
    }): Promise<void>;
    /**
     * Wait for selector
     */
    waitForSelector(selector: string, options?: {
        timeout?: number;
        state?: 'attached' | 'detached' | 'visible' | 'hidden';
    }): Promise<void>;
    /**
     * Get element attribute
     */
    getAttribute(selector: string, attribute: string): Promise<string | null>;
    /**
     * Check if element exists
     */
    elementExists(selector: string): Promise<boolean>;
    /**
     * Get page title
     */
    getTitle(): Promise<string>;
    /**
     * Get current URL
     */
    getCurrentUrl(): Promise<string>;
    /**
     * Execute JavaScript on page
     */
    evaluateScript<T>(script: string): Promise<T>;
    /**
     * Upload file
     */
    uploadFile(selector: string, filePath: string): Promise<void>;
    /**
     * Select option from dropdown
     */
    selectOption(selector: string, value: string): Promise<void>;
    /**
     * Get page HTML
     */
    getPageHtml(): Promise<string>;
    /**
     * Wait for a specific time
     */
    wait(milliseconds: number): Promise<void>;
    /**
     * Hover over element
     */
    hover(selector: string): Promise<void>;
    /**
     * Get all links from page
     */
    getAllLinks(): Promise<Array<{
        text: string;
        href: string;
    }>>;
    /**
     * Get form data
     */
    getFormData(formSelector: string): Promise<Record<string, string>>;
    /**
     * Check checkbox or radio button
     */
    check(selector: string): Promise<void>;
    /**
     * Uncheck checkbox
     */
    uncheck(selector: string): Promise<void>;
    /**
     * Get element count
     */
    count(selector: string): Promise<number>;
    /**
     * Scroll to element
     */
    scrollTo(selector: string): Promise<void>;
    /**
     * Get cookies
     */
    getCookies(): Promise<any[]>;
    /**
     * Set cookies
     */
    setCookies(cookies: any[]): Promise<void>;
    /**
     * Clear cookies
     */
    clearCookies(): Promise<void>;
}
//# sourceMappingURL=browser-automation.d.ts.map