import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';

export class CookiesConsent extends Component {
  constructor() {
    super('RGPDConsent', false);
  }

  public async init(): Promise<void> {
    await super.init();

    const cookiesClosed = localStorage.getItem('hw-cookies-closed');

    if (cookiesClosed != 'true') $('#hw-cookies-consent').removeClass('d-none');

    $('#hw-cookies-btn-configure').on('click', (event: MouseEvent) => {
      event.preventDefault();

      $('#hw-cookies-consent-introduction').addClass('d-none');
      $('#hw-cookies-consent-details').removeClass('d-none');
    });

    $('#hw-cookies-close, #hw-cookies-btn-agree, #hw-cookies-btn-close').on('click', (event: MouseEvent) => {
      event.preventDefault();

      $('#hw-cookies-consent').addClass('d-none');

      localStorage.setItem('hw-cookies-closed', 'true');
    });

    this.success();
  }
}
