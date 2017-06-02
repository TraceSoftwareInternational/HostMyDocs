import './vendor';

import { HostMyDocs } from './modules/hostMyDocs.module';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { bootloader } from "@angularclass/hmr";

export function main() {
    return platformBrowserDynamic().bootstrapModule(HostMyDocs);
}

// boot on document ready
bootloader(main);
