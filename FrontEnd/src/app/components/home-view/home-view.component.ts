import * as Clipboard from 'clipboard';

import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';

import { Location } from '@angular/common';
import { ProjectInfo } from '../../models/ProjectInfo';

import { UrlExistenceService } from '../../services/url-existence.service';

@Component({
  selector: 'tsi-home-view',
  templateUrl: './home-view.component.html',
  styleUrls: ['./home-view.component.sass']
})
export class HomeViewComponent implements OnInit {
    /**
         * Information about the current documentation (project name, version and language)
         */
    currentState: ProjectInfo;

    /**
     * Full URL to the archive to download
     */
    downloadLink: string;

    /**
     * Full URL to the current documentation embedded in HostMyDocs
     */
    embeddedSharingLink: string;

    /**
     * Full URL to the current documentation without HostMyDocs
     */
    standaloneSharingLink: string;

    /**
     * Helper to hide or show side navigation
     */
    hideSidenav: boolean;

    /**
     * Parameters that will be appended to the current URL
     */
    urlParams: string;

    /**
     * Shape of the clrIcon that triggers dropdown of copy actions
     */
    copyIconShape = 'share';

    /**
     * helper to know if viewer is empty or not
     */
    isProjectSelected = false;

    /**
     * Shows if any error was encountered while loading project
     */
    loadingError = false;

    /**
     * Full URL of the current page to display in the iframe
     */
    urlToDisplay: string;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private location: Location,
        private UrlExistence: UrlExistenceService
    ) { }

    /**
     * Tries to read URL params to set a certain state.
     */
    ngOnInit(): void {
        this.route.params.subscribe((val) => {
            if (val !== {}) {
                const routeParams = JSON.parse(JSON.stringify(val), ProjectInfo.reviver);

                if (routeParams !== undefined) {
                    if (routeParams.isValid()) {
                        this.currentState = routeParams;
                        this.hideSidenav = true;
                        this.openDocumentation(this.currentState);
                    } else {
                        this.hideSidenav = false;
                    }
                }
            }
        });
    }

    /**
     * Change the state of to boolean that control sidenav visibility
     */
    toggleSidenav(): void {
        this.hideSidenav = !this.hideSidenav;
    }

    /**
     * Change the current URL using this.urlParams content.
     */
    updateUrlBar(): void {
        this.location.replaceState('/view' + this.currentState.getMatrixNotation());
    }

    /**
     * Change the shape of the copy icon on success or error
     * Function to be a callback of the TsiClipboard
     */
    afterClipboardAction(status: boolean) {
        const originalValue = this.copyIconShape;

        if (status) {
            this.copyIconShape = 'success';
        } else {
            this.copyIconShape = 'error';
        }

        setTimeout(() => this.copyIconShape = originalValue, 1000);
    }

    /**
     * Receive event and propagate it to the documentation-viewer component
     */
    openDocumentation(event: ProjectInfo): void {
        const promise = this.UrlExistence.check(event.getBestURL());

        promise.then((urlExists) => {
            this.currentState = event;

            this.urlToDisplay = window.location.origin + this.currentState.getBestURL();

            this.updateUrlBar();

            this.isProjectSelected = true;
            this.loadingError = false;
        }).catch(() => {
            this.isProjectSelected = false;
            this.loadingError = true;
        });
    }


    /**
     * Watch the src attribute to update currentState and sharing links for any page change
     */
    iframeSrcWatcher(iframe) {
        const fullUrl = iframe.contentWindow.location.href;
        const relativeUrl = fullUrl.replace(iframe.contentWindow.location.origin, '');

        if (relativeUrl !== undefined) {
            this.currentState.setCurrentPage(relativeUrl);

            this.updateUrlBar();

            this.downloadLink = window.location.origin + this.currentState.getArchiveFile();
            this.embeddedSharingLink = window.location.origin + '/#/view' + this.currentState.getMatrixNotation();
            this.standaloneSharingLink = window.location.origin + this.currentState.getBestURL();
        }
    }
}
