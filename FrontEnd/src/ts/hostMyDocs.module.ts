import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { SearchView } from './components/search-view/index';

@NgModule({
    imports: [BrowserModule],
    declarations: [SearchView],
    bootstrap: [SearchView]
})

export class HostMyDocs { }
