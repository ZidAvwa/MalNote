import { test, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

test('test', async ({ page }) => {
    await page.goto('http://localhost:8000/');
    await page.getByRole('textbox', { name: 'MAL username' }).click();
    await page.getByRole('textbox', { name: 'MAL username' }).fill('Av1dz');
    await page.getByRole('textbox', { name: 'MAL user id (numbers only)' }).click();
    await page.getByRole('textbox', { name: 'MAL user id (numbers only)' }).fill('16505659');
    await page
        .locator('input[type="file"]')
        .setInputFiles(path.join(__dirname, 'fixtures', 'list.txt'));
    await page.getByRole('button', { name: 'Upload & Start' }).click();
    await page.getByText('Shingeki no Kyojin (TV, 2013)').click();
    await page.getByText('(5) Average').click();
    await page.getByText('(5) Average').check();
    await page.getByText('Dropped').click();
    await page.getByText('Dropped').check();
    await page.getByRole('button', { name: 'Submit & Next' }).click();
    await page.getByText('Plan to Watch').click();
    await page.getByText('Plan to Watch').check();
    await page.getByRole('button', { name: 'Submit & Next' }).click();
    const download1Promise = page.waitForEvent('download');
    await page.getByRole('link', { name: 'Download XML' }).click();
    const download1 = await download1Promise;
    await page.getByRole('link', { name: '← All imports' }).click();
});