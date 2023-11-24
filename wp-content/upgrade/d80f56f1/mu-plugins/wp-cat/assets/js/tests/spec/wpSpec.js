window.wp = window.wp || {};

describe("wp", function() {

  it("should be global", function() {
    a = true;
    expect(window.wp).toEqual(jasmine.any(Object));
  });
});