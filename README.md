# hanako-wp
A basic wordpress template based on Timber/Twig and Hanako ts framework.

## how to use
To run webpack compilation:

```npm run webpack```

To add a new page/module/resource, etc. (using hanako-cli)
```hanako add```

## ideas

- ...

## Version

Last release 09.05.2022

### 1.4.15
- Remove ACF json save
– Debug option true by default
- Whe debug is activate css and js are invalidated on reload by ?time=xxx

### 1.4.14

- Fix OB Flush warning
– Add ACF escaping code
– Fix RGPD Consent box
– Update hanako-ts to 1.2.2
– Fix PHP Warning due to update removal hook in menus

### 1.4.13

- Views structure update
- PHP 8
- Bugfix

### 1.4.12

- Minor renaming

### 1.4.11

- move Debug to views/ts/components
- add some function into Twig Functions (print_r)
- Image Layzloading !!!

### 1.4.10

- views structure update

### 1.4.9

- add responsive sizing class in Boostrap

### 1.4.8

- add Swiper.ts helper to handle swipe on touch devices

### 1.4.7

- package.json update and some polishing.

### 1.4.6

- Add comment disabling in backend options

### 1.4.5
- Add CSS font smoothing
- Remove compile .js
- Set Boostrap to 12 columns
- Fix models/single.php

### 1.4.4

- Update to Boostrap 5.1

### 1.4.3

- RGPD consent
- Translation files

### 1.4.2

- Enhenced redirection system for non logged user

### 1.4.1

- Bugfix in compile script

### 1.4.0

- Directory structure refactoring (again). The system now use a "MVC" inspired structure.

### 1.3.0

- Directory structure refactoring
- New webpack workflow

### 1.2.0

- Add Timmy support
- New install notices system

### 1.1.1

Various enhancement 
- add Webpack entry point for editor-style.css
- update scss file structure
- add scss file for styling language selector
- update webpack config

### 1.1.0

Switch from global WP structure to theme only

### 1.0

Brand new system based on Timber/Twig, SCSS and Typescript
