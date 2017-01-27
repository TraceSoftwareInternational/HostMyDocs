import { Component } from '@angular/core';
import { OnInit } from '@angular/core';

import { DummyData } from '../../services/dummyData.service';

@Component({
    selector: 'search-view',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass'],
    providers: [DummyData]
})

export class SearchView implements OnInit {
    constructor(private dummyData: DummyData) {}

    ngOnInit(): void {
        this.projects = this.dummyData.getProjects();
    }

    name = 'GitHub';
    projects = null;
}
