import { $ } from 'hanako-ts/dist-legacy/Framework';
import { Component } from 'hanako-ts/dist-legacy/Component';
import { Collection } from 'hanako-ts/dist-legacy/Collection';

type Theme = 'auto' | 'light' | 'dark';

export class DarkMode extends Component {
  private storedTheme = localStorage.getItem('theme');

  constructor() {
    super('DarkMode', false);
  }

  public async init(): Promise<void> {
    await super.init();

    const theme = this.getPreferredTheme()
    this.setTheme(theme);
    this.showActiveTheme(theme);

    $('[data-bs-theme-value]').each((toggle: Collection) => {
      toggle.on('click', () => {
        const theme = <Theme>toggle.attr('data-bs-theme-value');
        localStorage.setItem('theme', theme)
        this.setTheme(theme);
        this.showActiveTheme(theme);
      });
    });

    this.success();
  }

  private getPreferredTheme(): Theme {
    if (this.storedTheme) return <Theme>this.storedTheme;

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  private setTheme(theme: Theme) {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      $('body').attr('data-bs-theme', 'dark');
    } else {
      $('body').attr('data-bs-theme', theme);
    }
  }

  private showActiveTheme(theme: Theme) {
    const themeSwitcher = $('#bd-theme');
    if (!themeSwitcher) return;

    const themeSwitcherText = $('#bd-theme-text');
    const activeThemeIcon = $('.theme-icon-active');
    const btnToActive = $('[data-bs-theme-value="' + theme + '"]');

    $('[data-bs-theme-value]').each((element: Collection) => {
      element.removeClass('active');
      element.attr('aria-pressed', 'false');
    });

    btnToActive.addClass('active');
    btnToActive.attr('aria-pressed', 'true');
    activeThemeIcon.removeClass('bi-color-theme-light bi-color-theme-dark bi-color-theme-auto').addClass('bi-color-theme-' + theme);
  }
}
