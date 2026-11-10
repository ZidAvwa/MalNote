import { test, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

test('upload a list, review titles, and download the XML', async ({ page }) => {
  await page.goto('/');
  await page.getByRole('textbox', { name: 'MAL username' }).fill('Av1dz');
  await page.getByRole('textbox', { name: 'MAL user id (numbers only)' }).fill('16505659');
  await page
    .locator('input[type="file"]')
    .setInputFiles(path.join(__dirname, 'fixtures', 'list.txt'));
  await page.getByRole('button', { name: 'Upload & Start' }).click();

  // Title 1: wait for the AniList result before choosing anything
  const first = page.getByText('Shingeki no Kyojin (TV, 2013)');
  await expect(first).toBeVisible({ timeout: 30_000 });
  await first.click();
  await page.getByText('(5) Average').click();
  await page.getByText('Dropped').click();
  await page.getByRole('button', { name: 'Submit & Next' }).click();

  // Title 2: same idea
  await expect(page.getByText('Progress: 2/2')).toBeVisible();
  const second = page.getByText('DEATH NOTE (TV, 2006)');
  await expect(second).toBeVisible({ timeout: 30_000 });
  await second.click();
  await page.getByText('Plan to Watch').click();
  await page.getByRole('button', { name: 'Submit & Next' }).click();

  // Finished page: the download link should now exist
  const downloadLink = page.getByRole('link', { name: 'Download XML' });
  await expect(downloadLink).toBeVisible({ timeout: 15_000 });

  const downloadPromise = page.waitForEvent('download');
  await downloadLink.click();
  const download = await downloadPromise;
  expect(download.suggestedFilename()).toMatch(/^mal_.*\.xml$/);
});