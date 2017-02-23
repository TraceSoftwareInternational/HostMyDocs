import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { HomeView } from '../components/home-view/home-view.component';

/**
 * Declaring all the application routes in one place
 */
let routes: Routes = [
    { path: 'view', component: HomeView },
    { path: '', redirectTo: '/view', pathMatch: 'full' },
    { path: '**', redirectTo: '/view' }
];

@NgModule({
    imports: [
        RouterModule.forRoot(routes, { useHash: true })
    ],
    exports: [RouterModule]
})
export class Routing {}
