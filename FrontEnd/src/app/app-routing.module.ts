import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { HomeViewComponent } from './components/home-view/home-view.component';

const routes: Routes = [
  { path: 'view', component: HomeViewComponent },
  { path: '', redirectTo: '/view', pathMatch: 'full' },
  { path: '**', redirectTo: '/view' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, {useHash: true})],
  exports: [RouterModule]
})
export class AppRoutingModule { }
