import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { ClarityModule } from 'clarity-angular';

import { SearchView } from './components/search-view/index';

@NgModule({
    imports: [
        BrowserModule,
        ClarityModule.forRoot()
    ],
    declarations: [SearchView],
    bootstrap: [SearchView]
})

export class HostMyDocs { }
