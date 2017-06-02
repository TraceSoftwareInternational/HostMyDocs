import { ApplicationRef, NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';

import { ClarityModule } from 'clarity-angular';

import { Routing } from '../modules/routing.module';

import { AppRoot }      from '../components/app-root/index';
import { LandingPage }  from '../components/landing-page/landing-page.component';
import { HomeView }     from '../components/home-view/home-view.component';
import { ProjectsTree } from '../components/projects-tree/project-tree.component';

import { TsiClipboard } from '../directives/tsiClipboard.directive';

import { FilterProjects } from '../pipes/FilterProjects.pipe';
import { TrustUrl } from '../pipes/TrustUrl.pipe';

// HMR relative import
import { removeNgStyles, createNewHosts, createInputTransfer } from '@angularclass/hmr';

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
        HomeView,
        LandingPage,
        ProjectsTree,
        TsiClipboard,
        TrustUrl
    ],
    bootstrap: [AppRoot]
})
export class HostMyDocs {
    constructor(public appRef: ApplicationRef) { }

    hmrOnInit(store) {
        if (!store || !store.state) return;

        if ('restoreInputValues' in store) {
            store.restoreInputValues();
        }
        // change detection
        this.appRef.tick();
        delete store.state;
        delete store.restoreInputValues;
    }

    hmrOnDestroy(store) {
        var cmpLocation = this.appRef.components.map(cmp => cmp.location.nativeElement);
        // recreate elements
        store.disposeOldHosts = createNewHosts(cmpLocation)
        // save input values
        store.restoreInputValues = createInputTransfer();
        // remove styles
        removeNgStyles();
    }

    hmrAfterDestroy(store) {
        // display new elements
        store.disposeOldHosts()
        delete store.disposeOldHosts;
        // anything you need done the component is removed
    }
}
