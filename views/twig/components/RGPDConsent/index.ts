import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';

export class RGPDConsent extends Component {
  constructor() {
    super('RGPDConsent', false);
  }

  public async init(): Promise<void> {
    await super.init();

    const grpgClosed = localStorage.getItem('ab-grpg-closed');

    if (grpgClosed != 'true') $('#ab-rgpb-consent').removeClass('d-none');

    $('#ab-rgpd-btn-configure').on('click', (event: MouseEvent) => {
      event.preventDefault();

      $('#ab-rgpb-consent-introduction').addClass('d-none');
      $('#ab-rgpb-consent-details').removeClass('d-none');
    });

    $('#ab-rgpd-btn-back').on('click', (event: MouseEvent) => {
      event.preventDefault();

      $('#ab-rgpb-consent-introduction').removeClass('d-none');
      $('#ab-rgpb-consent-details').addClass('d-none');
    });

    $('#ab-rgpb-close, #ab-rgpd-btn-agree').on('click', (event: MouseEvent) => {
      event.preventDefault();

      $('#ab-rgpb-consent').addClass('d-none');

      localStorage.setItem('ab-grpg-closed', 'true');
    });

    this.success();
  }
}
