"""Run with Python Playwright against a staging page that has Fluent Forms Pro.

    python tests/native-quote-browser.py https://your-staging-host/prices/

Only operates the public estimator controls. No enquiry is submitted.
"""
import argparse
from decimal import Decimal
import re

from playwright.sync_api import expect, sync_playwright


def check_estimator(page, url):
    page.goto(url, wait_until='domcontentloaded')
    form = page.locator('form.frm-fluent-form').filter(has=page.locator('select[name="home_size"]'))
    expect(form).to_have_count(1)

    def field(name):
        return form.locator(f'[name="{name}"]')

    def choose(name, value):
        field(name).select_option(value=value)

    def amount(name, value):
        expected = Decimal(str(value))
        pattern = re.escape(format(expected, 'f'))
        if expected == expected.to_integral_value():
            pattern += r'(?:\.0+)?'
        expect(field(name)).to_have_value(re.compile('^' + pattern + '$'), timeout=10000)

    def estimate(total, low, high):
        expect(field('estimate_low')).to_be_visible()
        amount('estimate_total', total)
        amount('estimate_low', low)
        amount('estimate_high', high)

    def check_extra(group, value):
        form.locator(f'input[data-name="{group}"][value="{value}"]').check()

    choose('service', 'Regular cleaning')
    choose('suburb', 'Carnegie 3163')
    choose('condition', 'Standard condition')
    choose('home_size', '3 bedrooms · 2 bathrooms')
    choose('frequency', 'Fortnightly')
    estimate(204, 185, 225)

    choose('service', 'End of lease cleaning')
    choose('empty', 'Yes, empty')
    choose('lease_size', '2 bedrooms · 1 bathroom')
    choose('oven', 'Standard single oven')
    choose('suburb', 'Glen Waverley 3150')
    estimate(460, 415, 505)

    choose('service', 'Deep or spring cleaning')
    choose('home_size', '4 bedrooms · 2 bathrooms')
    choose('storeys', 'Double storey')
    choose('oven', 'No oven clean')
    choose('suburb', 'Bentleigh 3204')
    check_extra('extras', 'Inside fridge')
    estimate('505.75', 455, 555)
    check_extra('extra_interiors', 'Inside windows')
    check_extra('extra_interiors', 'Inside cupboards')
    choose('service', 'End of lease cleaning')
    choose('empty', 'Yes, empty')
    choose('lease_size', '2 bedrooms · 1 bathroom')
    choose('storeys', 'Single storey')
    expect(form.locator('input[value="Inside windows"]')).to_be_hidden()
    estimate(380, 340, 420)

    choose('service', 'Carpet steam cleaning')
    check_extra('areas', 'Bedroom')
    field('bedrooms_carpet').fill('1')
    estimate(110, 110, 120)

    choose('suburb', 'My suburb isn’t listed')
    expect(field('estimate_low')).to_be_hidden()
    expect(field('estimate_high')).to_be_hidden()
    expect(form.get_by_text('We’ll confirm availability and travel for this suburb.')).to_be_visible()
    expect(field('name')).to_be_visible()


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('url', help='WordPress staging URL of the quote page')
    args = parser.parse_args()
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch()
        try:
            check_estimator(browser.new_page(viewport={'width': 390, 'height': 844}), args.url)
        finally:
            browser.close()
    print('All staging quote checks passed.')
