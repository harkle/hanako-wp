import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import BS_Carousel from 'bootstrap/js/dist/carousel';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class Carousel extends Component {
  constructor() {
    super('Carousel', false);
  }

  public async init(): Promise<void> {
    await super.init();

    $('.carousel').each((carousel: Collection) => {
      new BS_Carousel(carousel.get(0));
    });

    this.success();
  }
}
