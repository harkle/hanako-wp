import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';

export class ModuleDemo extends Component {
  constructor() {
    super('Empty', false);
  }

  public async init(): Promise<void> {
    await super.init();

    this.success();
  }
}
