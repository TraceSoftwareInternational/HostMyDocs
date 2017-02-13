export class ProjectChangeEvent {
    constructor(
        private indexPath: string,
        private archivePath: string
    ) {}

    public getIndexPath() : string {
        return this.indexPath;
    }

    public getArchivePath() : string {
        return this.archivePath;
    }
}
