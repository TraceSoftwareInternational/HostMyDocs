import './vendor';

import { HostMyDocs } from './modules/hostMyDocs.module';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { enableProdMode } from '@angular/core';

export function main() {
    enableProdMode();
    platformBrowserDynamic().bootstrapModule(HostMyDocs);
}

function bootloader(main) {
    if (document.readyState === 'complete') {
        main()
    } else {
        document.addEventListener('DOMContentLoaded', main);
    }
}

bootloader(main);
