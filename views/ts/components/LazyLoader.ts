import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class LazyLoader extends Component {
  private static tolerence = 100;
  private static images: Collection;
  private static backgroundImages: Collection;

  constructor() {
    super('LazyLoader', false);
  }

  public async init(): Promise<void> {
    await super.init();

    LazyLoader.images = $('*[data-hw-src]');
    LazyLoader.backgroundImages = $('*[data-hw-background-image]');

    $(window).on('scroll', () => {
      LazyLoader.checkImageVisibility();
    });

    LazyLoader.checkImageVisibility();

    this.success();
  }

  public static checkImageVisibility() {
    LazyLoader.images.each((image: Collection) => {
      if (image.viewportPosition().y < $(window).height() + LazyLoader.tolerence && image.data('backgroundLoaded') != 'true') {
        image.attr('src', image.data('hwSrc'));
        image.data('backgroundLoaded', 'true');
      }
    });

    LazyLoader.backgroundImages.each((image: Collection) => {
      if (image.viewportPosition().y < $(window).height() + LazyLoader.tolerence && image.data('backgroundLoaded') != 'true') {
        image.css('background-image', 'url(\'' + image.data('hwBackgroundImage') + '\')');
        image.data('backgroundLoaded', 'true');
      }
    });
  }
}
