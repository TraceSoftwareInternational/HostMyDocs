import { TestBed, inject } from '@angular/core/testing';

import { UrlExistenceService } from './url-existence.service';

describe('UrlExistenceService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [UrlExistenceService]
    });
  });

  it('should be created', inject([UrlExistenceService], (service: UrlExistenceService) => {
    expect(service).toBeTruthy();
  }));
});
