import { Component, Input, Output, EventEmitter } from '@angular/core'
import { OnInit } from '@angular/core';

import { ProjectsService } from '../../services/projects.service';

import { Project }  from '../../models/Project';
import { Version }  from '../../models/Version';
import { Language } from '../../models/Language';
import { ProjectInfo } from '../../models/ProjectInfo';

@Component({
    selector: 'project-tree',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass'],
    providers: [ProjectsService]
})
export class ProjectsTree implements OnInit {
    /**
     * Name of the project to open
     */
    @Input() project: string;

    /**
     * Version to open provided by URL
     */
    @Input() version: string;

    /**
     * Language to open provided by URL
     */
    @Input() language: string;

    /**
     * Event emitter to notify parent component of archivePath and indexPath from the selected language
     */
    @Output() onProjectSelection = new EventEmitter<ProjectInfo>();

    private projects: Array<Project> = [];

    constructor(private projectsService: ProjectsService) {}

    /**
     * Fetch projects from BackEnd at component initialization.
     * Also tries to restore a certain state provided by project, version and language
     */
    ngOnInit(): void {
        this.projectsService.getProjects().subscribe(
            (projects) => {
                this.projects = projects;
                this.initializeFromParameters();
            },
            error => console.log(error)
        );
    }

    /**
     * If this.project, this.version and this.language are initialized, open the corresponding documentation
     */
    initializeFromParameters(): void {
        if (this.project !== null && this.version !== null && this.language !== null) {
            let expectedProject = this.retriveExpectedState();

            if (expectedProject !== null) {
                this.onProjectSelection.emit(expectedProject);
            }
        }
    }

    /**
     * Using provided information, build a ProjectInfo object
     */
    retriveExpectedState(): ProjectInfo|null  {
        for (let project of this.projects) {
            if (project.name === this.project) {
                for (let version of project.versions) {
                    if (version.number === this.version) {
                        for (let language of version.languages) {
                            let state = new ProjectInfo(this.project, this.version, this.language);
                            state.setArchiveFile(language.archivePath);
                            state.setindexFile(language.indexPath);

                            return state;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Given a project, return its highest version
     */
    getLastVersion(project: Project) : string {
        let lastVersionIndex = project.versions.length - 1;

        return project.versions[lastVersionIndex].number;
    }

    /**
     * Sending an event to the parent, to display documentation
     */
    notifyParent(project: Project, version: Version, language: Language) : void {
        let state = new ProjectInfo(project.name, version.number, language.name);
        state.setArchiveFile(language.archivePath);
        state.setindexFile(language.indexPath);

        this.onProjectSelection.emit(state);
    }
}
