import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';

export class Page extends Component {
  constructor() {
    super('Empty', false);
  }

  public async init(): Promise<void> {
    await super.init();

    this.success();
  }
}
