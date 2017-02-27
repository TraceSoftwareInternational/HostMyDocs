import './vendor';

import { HostMyDocs } from './modules/hostMyDocs.module';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

platformBrowserDynamic().bootstrapModule(HostMyDocs);
