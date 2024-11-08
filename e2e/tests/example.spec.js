// @ts-check
const { test, expect } = require('@playwright/test');

test('homepage', async ({ page }) => {
  await page.goto('http://php/');
  await expect(page).toHaveTitle('Welcome to API Platform!');
});

test('swagger', async ({ page }) => {
  await page.goto('http://php/docs');
  await expect(page).toHaveTitle('Hello API Platform - API Platform');
  await expect(page.locator('.operation-tag-content > span')).toHaveCount(5);
});

test('admin', async ({ page }) => {
  await page.goto('http://php/admin');
	await page.getByLabel('Create').click();
	await page.getByLabel('Name').fill('foo');
	await page.getByLabel('Save').click();
  await expect(page).toHaveURL(/admin#\/greetings$/);
	await expect(page.getByText('foo')).toHaveCount(1);
	await page.getByText('foo').first().click();
  await expect(page).toHaveURL(/show$/);
	await page.getByLabel('Edit').click();
	await page.getByLabel('Name').fill('bar');
	await page.getByLabel('Save').click();
	await expect(page.getByText('bar')).toHaveCount(1);
	await page.getByLabel('Edit').click();
	await page.getByLabel('Delete').click();
});
