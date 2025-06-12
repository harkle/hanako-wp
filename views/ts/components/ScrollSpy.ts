import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class ScrollSpy extends Component {
  private links: Collection;
  private sections: Collection;
  private headerHeight: number;

  constructor() {
    super('ScrollSpy', false);

    this.headerHeight = 0;
  }

  public async init() {
    await super.init();

    this.sections = $('.hw-scrollspy-section');
    this.links = $('.hw-scrollspy-menu a');

    this.links.on('click', (event: Event, link: Collection) => {
      event.preventDefault();

      $.scrollTo($(link.attr('href')).position().y + -this.headerHeight, 500);
    });

    $(window).on('scroll', () => {
      this.spy();
    });

    $(window).on('resize', () => {
      this.updateHeaderHeight();
    });

    this.updateHeaderHeight();
    this.spy();

    this.success();
  }

  public spy(): void {
    var currentID = this.sections.eq(0).attr('id');

    this.sections.each((section: Collection) => {
      if (Math.floor(section.viewportPosition().y) <= this.headerHeight) currentID = $(section).attr('id');
    });

    if (currentID && this.links.search('[href*="#' + currentID + '"]').length > 0) {
      this.links
        .removeClass('active')
        .search('[href*="#' + currentID + '"]')
        .addClass('active');
    }
  }

  private updateHeaderHeight() {
    this.headerHeight = $('.hw-scrollspy-header').height();
  }
}
