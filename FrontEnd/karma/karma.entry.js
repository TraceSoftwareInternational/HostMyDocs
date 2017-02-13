import 'core-js/es6';
import 'core-js/es7/reflect';
import 'core-js/shim';
import 'reflect-metadata';
import 'zone.js/dist/zone';
import 'zone.js/dist/long-stack-trace-zone';
import 'zone.js/dist/proxy';
import 'zone.js/dist/sync-test';
import 'zone.js/dist/jasmine-patch';
import 'zone.js/dist/async-test';
import 'zone.js/dist/fake-async-test';

const testContext = require.context('../src', true, /\.spec\.ts/);
testContext.keys().forEach(testContext);

import * as testing from '@angular/core/testing';
import * as browser from '@angular/platform-browser-dynamic/testing';

testing.TestBed.initTestEnvironment(browser.BrowserDynamicTestingModule, browser.platformBrowserDynamicTesting());
