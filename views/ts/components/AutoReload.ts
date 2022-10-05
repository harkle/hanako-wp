import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class AutoReload extends Component {
  private lastTime: number = 0;

  constructor() {
    super('AutoReload', false);
  }

  public async init(): Promise<void> {
    await super.init();

    if ($('body').hasClass('auto-reload')) {
      setInterval(async () => {
        const response: string = await $.httpRequest({
          url: '/wp-admin/admin-ajax.php',
          type: 'POST',
          body: {
            'action': 'get_assets_date'
          },
          dataType: 'json'
        });
        
        if (parseInt(response) != this.lastTime && this.lastTime != 0) location.reload();
        
        this.lastTime = parseInt(response);
      }, 2500);
    }

    this.success();
  }
}
