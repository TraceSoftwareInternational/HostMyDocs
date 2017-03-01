import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule }    from '@angular/http';

import { ClarityModule } from 'clarity-angular';

import { ClipboardModule } from 'ngx-clipboard';

import { Routing } from '../modules/routing.module';

import { AppRoot }             from '../components/app-root/index';
import { DocumentationViewer } from '../components/documentation-viewer/documentation-viewer.component';
import { HomeView }            from '../components/home-view/home-view.component';
import { ProjectsTree }        from '../components/projects-tree/project-tree.component';

import { TsiClipboard } from '../directives/tsiClipboard.directive';

import { TrustUrl } from '../pipes/TrustUrl.pipe';

/**
 * Main module of the application
 */
@NgModule({
    imports: [
        ClipboardModule,
        BrowserModule,
        HttpModule,
        ClarityModule.forChild(),
        Routing
    ],
    declarations: [
        AppRoot,
        DocumentationViewer,
        ProjectsTree,
        HomeView,
        TsiClipboard,
        TrustUrl
    ],
    bootstrap: [AppRoot]
})

export class HostMyDocs { }
