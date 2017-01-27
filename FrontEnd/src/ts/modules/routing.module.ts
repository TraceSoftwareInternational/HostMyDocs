import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { SearchView } from '../components/search-view/index';

/**
 * Declaring all the application routes in one place
 */
let routes: Routes = [
    { path: '', component: SearchView }
];

@NgModule({
    imports: [
        RouterModule.forRoot(routes)
    ],
    exports: [RouterModule]
})
export class Routing {}
