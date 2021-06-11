import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Collection } from 'hanako-ts/dist-legacy/Collection';
import { Component } from 'hanako-ts/dist-legacy/Component';
import { traverse } from './Traverse';

export class Debug extends Component {
  private isDebugEnabled: boolean;

  constructor(isDebugEnabled: boolean = false) {
    super('Debug', false);

    this.isDebugEnabled = isDebugEnabled;
  }

  public async init(): Promise<void> {
    await super.init();

    this.setupConsole();
    this.test();

    this.success();
  }

  private setupConsole() {
    if (!$('body').hasClass('debug')) return;

    let consoleElement = $.parseHTML('<div class="hanako-terminal hanako-terminal-debug"><pre><code id="debug-console"></code></pre></div>');

    $('body').append(consoleElement);

    let consoleConfig = JSON.parse(sessionStorage.getItem('hanako-debug'));
    if (!consoleConfig) consoleConfig = { position: 'bottom', status: 'out' }

    consoleElement.addClass(consoleConfig.position);
    consoleElement.addClass(consoleConfig.status);

    $(document).on('keyup', (event: KeyboardEvent) => {
      if (event.altKey && event.code == 'ArrowUp') {
        consoleElement.addClass('top').removeClass('bottom');
        consoleConfig.position = 'top';
        sessionStorage.setItem('hanako-debug', JSON.stringify(consoleConfig));
      }

      if (event.altKey && event.code == 'ArrowDown') {
        consoleElement.addClass('bottom').removeClass('top');
        consoleConfig.position = 'bottom';
        sessionStorage.setItem('hanako-debug', JSON.stringify(consoleConfig));
      }

      if (event.altKey && event.code == 'KeyD') {
        consoleElement.toggleClass('in');
        consoleConfig.status = (consoleElement.hasClass('in')) ? 'in' : 'out';
        sessionStorage.setItem('hanako-debug', JSON.stringify(consoleConfig));
      }

      if (event.altKey && event.code == 'KeyC') $('#debug-console').empty();
    });
  }

  private async test() {
    if (!$('body').hasClass('hanako-test')) return;

    Debug.print('test', 'Hello world! My name is <span class="key">Hanako</span> and I\'m here to help you.', '', '<br>', true);

    Debug.print('test', 'Please wait for images to load...', '', '<br>', true);

    await $.imagesLoaded($('img'));
    let images: Collection = $('img');

    Debug.print('test', images.length + ' images loaded', '', '<br>', true);

    $('img').each((image: Collection) => {
      Debug.print('test', image, '', '', true);
      Debug.print('test', '&#9;' + image.attr('src'), '', '<br>');
    });

    Debug.print('test', 'Everything seems to be okay. <span class="key">Enjoy your day...</span>', '', '<br>', true);

  }

  public static print(target: string, message: any, before: string = '', after: string = '', showTime: boolean = false) {
    let time = '';

    if (showTime) {
      const date: Date = new Date();

      const hours = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
      const minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
      const seconds = (date.getSeconds() < 10) ? '0' + date.getSeconds() : date.getSeconds();

      time = hours + ':' + minutes + ':' + seconds + '\t';
    }

    if (typeof message === 'object') message = traverse(message);
    if (typeof message === 'number') message = '<span class="attr">' + message + '</span>';

    $('#' + target + '-console').html(time + before + message + after, true);
  }

  public static log(message: any) {
    if (!$('body').hasClass('debug')) return;

    Debug.print('debug', message, '', '<br>', true);

    $('#debug-console').parent().get(0).scrollTop = $('#debug-console').parent().get(0).scrollHeight;
  }
}
