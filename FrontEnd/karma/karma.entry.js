import 'reflect-metadata';
import 'core-js/shim';
import 'zone.js/dist/zone';
import 'zone.js/dist/sync-test';
import 'zone.js/dist/async-test';
import 'zone.js/dist/fake-async-test';
import 'zone.js/dist/proxy';
import 'zone.js/dist/jasmine-patch';

const testContext = require.context('../src', true, /\.spec\.ts/);
testContext.keys().forEach(testContext);
