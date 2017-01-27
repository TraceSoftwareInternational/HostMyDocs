import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { ClarityModule } from 'clarity-angular';

import { Routing } from './routing.module';

import { AppRoot } from '../components/app-root/index';
import { SearchView } from '../components/search-view/index';

/**
 * Main module of the application
 */
@NgModule({
    imports: [
        BrowserModule,
        Routing,
        ClarityModule.forChild()
    ],
    declarations: [
        AppRoot,
        SearchView
    ],
    bootstrap: [AppRoot]
})

export class HostMyDocs { }
