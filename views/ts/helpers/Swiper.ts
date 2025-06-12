import { Collection } from 'hanako-ts/dist-legacy/Collection';
import { EventManager } from 'hanako-ts/dist-legacy/Tools/EventManager';
import { Elem, Selector, EventCallback } from 'hanako-ts/dist-legacy/Collection/Types';

export class Swiper {
  private element: Collection;

  private xDown: number;
  private yDown: number;
  private xDiff: number;
  private yDiff: number;

  constructor(element: Collection) {
    this.xDown = null;
    this.yDown = null;
    this.element = typeof element === 'string' ? document.querySelector(element) : element;

    this.element.on('touchstart', (event: TouchEvent) => {
      this.xDown = event.touches[0].clientX;
      this.yDown = event.touches[0].clientY;
    });

    this.element.on('touchmove', (event: TouchEvent) => {
      this.handleTouchMove(event);
    });
  }

  public on(eventNames: ('swipeLeft' | 'swipeRight' | 'swipeUp' | 'swipeDown') | Array<string>, callback: EventCallback): void {
    if (typeof eventNames == 'string') eventNames = [eventNames];

    eventNames.forEach((eventName: string) => {
      EventManager.add(this.element.get(0), eventName, null, callback, false);
    });
  }

  handleTouchMove(event: TouchEvent) {
    if (!this.xDown || !this.yDown) return;

    this.xDiff = this.xDown - event.touches[0].clientX;
    this.yDiff = this.yDown - event.touches[0].clientY;

    if (Math.abs(this.xDiff) < 100 && Math.abs(this.yDiff) < 100) return;

    if (Math.abs(this.xDiff) > Math.abs(this.yDiff)) {
      if (this.xDiff > 0) {
        this.trigger('swipeLeft');
      } else {
        this.trigger('swipeRight');
      }
    } else {
      if (this.yDiff > 0) {
        this.trigger('swipeUp');
      } else {
        this.trigger('swipeDown');
      }
    }

    this.xDown = null;
    this.yDown = null;
  }

  private trigger(eventName: string) {
    const event: Event = document.createEvent('HTMLEvents');
    event.initEvent(eventName, true, false);

    this.element.get(0).dispatchEvent(event);
  }
}
