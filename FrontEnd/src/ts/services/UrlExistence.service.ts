import { Injectable }     from '@angular/core';
import { Http, Response } from '@angular/http';

import { Observable } from 'rxjs/Observable';

/**
 * Simple service to check if an URL exists and answer to an HEAD request
 */
@Injectable()
export class UrlExistence {
    constructor(private http: Http) {}

    /**
     * Return true if the URL exist and return a 200 code
     */
    public check(url: string) : Observable<boolean> {
        return this.http.head(url)
            .map(() => { return true })
            .catch(() => Observable.throw(false))
    }
}
