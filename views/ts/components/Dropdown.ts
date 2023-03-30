import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import BS_Dropdown from 'bootstrap/js/dist/dropdown'
import { Collection } from 'hanako-ts/dist-legacy/Collection';

export class Dropdown extends Component {

  constructor(isDebugEnabled: boolean = false) {
    super('Dropdown', false);
  }

  public async init(): Promise<void> {
    await super.init();

    $('.dropdown-toggle').each((collapse: Collection) => {
      new BS_Dropdown(collapse.get(0));
    });

    this.success();
  }
}
