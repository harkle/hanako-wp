import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import BS_Collapse from 'bootstrap/js/dist/collapse';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class Collapse extends Component {
  constructor(isDebugEnabled: boolean = false) {
    super('Collapse', false);
  }

  public async init(): Promise<void> {
    await super.init();

    $('.collapse').each((collapse: Collection) => {
      new BS_Collapse(collapse.get(0), { toggle: false });
    });

    this.success();
  }
}
