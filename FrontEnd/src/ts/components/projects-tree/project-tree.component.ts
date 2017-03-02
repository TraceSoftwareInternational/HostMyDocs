import { Component, EventEmitter, Input, Output, OnInit } from '@angular/core'

import { ProjectsService } from '../../services/projects.service';

import { Language }    from '../../models/Language';
import { Version }     from '../../models/Version';
import { Project }     from '../../models/Project';
import { ProjectInfo } from '../../models/ProjectInfo';


@Component({
    selector: 'project-tree',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass'],
    providers: [ProjectsService]
})
export class ProjectsTree implements OnInit {
    /**
     * Event emitter to notify parent component of archivePath and indexPath from the selected language
     */
    @Output() onProjectSelection = new EventEmitter<ProjectInfo>();

    /**
     * Parameter for the tree filter pipe
     */
    filterText = '';

    /**
     * All projects sent by the server
     */
    projects: Array<Project> = [];

    constructor(private projectsService: ProjectsService) {}

    /**
     * Fetch projects from BackEnd at component initialization.
     */
    ngOnInit(): void {
        this.projectsService.getProjects().subscribe(
            projects => this.projects = projects,
            error => console.log(error)
        );
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
