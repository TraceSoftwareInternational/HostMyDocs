import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';

import { ClarityModule } from 'clarity-angular';

import { Routing } from '../modules/routing.module';

import { AppRoot }      from '../components/app-root/index';
import { HomeView }     from '../components/home-view/home-view.component';
import { ProjectsTree } from '../components/projects-tree/project-tree.component';

import { TsiClipboard } from '../directives/tsiClipboard.directive';

import { FilterProjects } from '../pipes/FilterProjects.pipe';
import { TrustUrl } from '../pipes/TrustUrl.pipe';

/**
 * Main module of the application
 */
@NgModule({
    imports: [
        BrowserAnimationsModule,
        BrowserModule,
        FormsModule,
        HttpModule,
        Routing,
        ClarityModule.forChild(),
    ],
    declarations: [
        AppRoot,
        FilterProjects,
        ProjectsTree,
        HomeView,
        TsiClipboard,
        TrustUrl
    ],
    bootstrap: [AppRoot]
})

export class HostMyDocs { }
