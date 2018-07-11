import { TrustUrlPipe } from './trust-url.pipe';

describe('TrustUrlPipe', () => {
  it('create an instance', () => {
    const pipe = new TrustUrlPipe();
    expect(pipe).toBeTruthy();
  });
});
