import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { TrustUrlPipe } from './pipes/trust-url.pipe';
import { FilterProjectsPipe } from './pipes/filter-projects.pipe';
import { AppRootComponent } from './components/app-root/app-root.component';
import { ClarityModule } from '@clr/angular';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HomeViewComponent } from './components/home-view/home-view.component';
import { LandingPageComponent } from './components/landing-page/landing-page.component';
import { ProjectsTreeComponent } from './components/projects-tree/projects-tree.component';
import { FormsModule } from '@angular/forms';

@NgModule({
    declarations: [
        TrustUrlPipe,
        FilterProjectsPipe,
        AppRootComponent,
        HomeViewComponent,
        LandingPageComponent,
        ProjectsTreeComponent
    ],
    imports: [
        FormsModule,
        BrowserAnimationsModule,
        BrowserModule,
        ClarityModule,
        AppRoutingModule,
        HttpClientModule
    ],
    providers: [],
    bootstrap: [AppRootComponent],
    exports: [TrustUrlPipe, FilterProjectsPipe]
})
export class AppModule { }
