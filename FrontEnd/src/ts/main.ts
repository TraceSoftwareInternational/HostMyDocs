import './vendor';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { HostMyDocs } from './modules/hostMyDocs.module';

platformBrowserDynamic().bootstrapModule(HostMyDocs);

