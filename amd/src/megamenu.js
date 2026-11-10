// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Opens and closes the mega menu and switches its community panels.
 *
 * On desktop the menu is a panel below the navbar. In the mobile drawer the communities are a second
 * level that replaces the drawer content (drill-down), see templates/megamenu_mobile*.mustache.
 *
 * @module     theme_lexa/megamenu
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    ROOT: '[data-region="lexa-megamenu"]',
    GRID: '[data-region="lexa-megamenu-grid"]',
    TOGGLE: '[data-action="lexa-megamenu-toggle"]',
    SELECT: '[data-action="lexa-megamenu-select"]',
    PANEL: '[data-region="lexa-megamenu-panel"]',
    NAVITEM: '[data-region="lexa-megamenu-navitem"]',
    HEADING: '.lexa-megamenu-heading',
    FOCUSABLE: 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])',
    ANIMKEY: '[data-animkey]',
    MOBILE_ROOT: '[data-region="lexa-megamenu-mobile"]',
    MOBILE_LEVEL1: '[data-region="lexa-megamenu-mobile-level1"]',
    MOBILE_PANEL: '[data-region="lexa-megamenu-mobile-panel"]',
    MOBILE_DRILL: '[data-action="lexa-megamenu-drill"]',
    MOBILE_BACK: '[data-action="lexa-megamenu-back"]',
    DRAWER_CONTENT: '.drawercontent',
};

const CLASSES = {
    SHOW: 'show',
    SELECTED: 'selected',
    LEAVING: 'leaving',
    FADEIN: 'lexa-megamenu-fadein',
    SWITCHING: 'switching',
    OPEN: 'lexa-megamenu-open',
    PREVIEW: 'lexa-megamenu-preview',
    MOBILE_PANELOPEN: 'lexa-mm-mobile-panelopen',
};

/** @var {number} Duration of the fade in and out of the menu in ms, like the Dissolve in the screen design. */
const FADEDURATION = 300;

/** @var {number} Duration of switching communities in ms, like the Smart Animate in the screen design. */
const SWITCHDURATION = 200;

/** @var {number} Duration of the mobile drill-down in ms, like the Smart Animate in the screen design. */
const DRILLDURATION = 250;

let initialised = false;
let closeTimer = null;

/** @var {Function|null} Finishes a running switch immediately. */
let finishSwitch = null;

/** @var {HTMLElement|null} The row that opened the current mobile panel, to return the focus to. */
let mobileOpener = null;

/**
 * Returns the mega menu root.
 *
 * @returns {HTMLElement|null}
 */
const getRoot = () => document.querySelector(SELECTORS.ROOT);

/**
 * Returns the root of the mobile mega menu in the drawer.
 *
 * @returns {HTMLElement|null}
 */
const getMobileRoot = () => document.querySelector(SELECTORS.MOBILE_ROOT);

/**
 * Returns all triggers. core/moremenu may move the trigger into the "More" dropdown, so it is looked up each time.
 *
 * @returns {HTMLElement[]}
 */
const getToggles = () => [...document.querySelectorAll(SELECTORS.TOGGLE)];

/**
 * Whether the mega menu is open.
 *
 * @returns {boolean}
 */
const isOpen = () => {
    const root = getRoot();
    return !!root && root.classList.contains(CLASSES.SHOW);
};

/**
 * Whether the user prefers reduced motion.
 *
 * @returns {boolean}
 */
const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Switches from one panel to another like Smart Animate in the screen design.
 *
 * The old panel fades out. Elements that exist in both panels (same data-animkey) move from their old
 * to their new position; if their content differs, the old content fades out and the new one fades in
 * while they move. All other new elements fade in. The height follows the new content.
 *
 * @param {HTMLElement} grid The element whose height follows, with both panels in the same grid cell.
 * @param {HTMLElement} current
 * @param {HTMLElement} next
 * @param {number} duration In ms.
 */
const animateSwitch = (grid, current, next, duration = SWITCHDURATION) => {
    const oldElements = new Map();
    current.querySelectorAll(SELECTORS.ANIMKEY).forEach((element) => {
        oldElements.set(element.dataset.animkey, {element, rect: element.getBoundingClientRect()});
    });

    // The old panel keeps its size, even if the new one makes the grid cell taller.
    current.style.height = `${current.offsetHeight}px`;

    // Measure the height with only the new panel.
    const startHeight = grid.offsetHeight;
    current.hidden = true;
    next.hidden = false;
    const endHeight = grid.offsetHeight;
    current.hidden = false;

    grid.classList.add(CLASSES.SWITCHING);
    current.classList.add(CLASSES.LEAVING);
    current.style.animationDuration = `${duration}ms`;

    const moved = [];
    const faded = [];
    const fadeIn = (element) => {
        element.classList.add(CLASSES.FADEIN);
        element.style.animationDuration = `${duration}ms`;
        faded.push(element);
    };
    const setTransform = (element, transform, transition) => {
        element.style.transition = transition;
        element.style.transform = transform;
    };

    next.querySelectorAll(SELECTORS.ANIMKEY).forEach((element) => {
        const old = oldElements.get(element.dataset.animkey);
        if (!old) {
            fadeIn(element);
            return;
        }
        const rect = element.getBoundingClientRect();
        const dx = rect.left - old.rect.left;
        const dy = rect.top - old.rect.top;
        const same = old.element.textContent.trim() === element.textContent.trim();
        if (same) {
            // Nothing to crossfade, the element only moves.
            old.element.style.visibility = 'hidden';
        } else {
            fadeIn(element);
        }
        if (Math.abs(dx) < 1 && Math.abs(dy) < 1) {
            return;
        }
        setTransform(element, `translate(${-dx}px, ${-dy}px)`, 'none');
        moved.push(element, old.element);
        if (!same) {
            // The old content moves along while it fades out.
            setTransform(old.element, 'translate(0, 0)', 'none');
        }
        old.target = `translate(${dx}px, ${dy}px)`;
    });

    grid.style.height = `${startHeight}px`;
    // Apply the start state before starting the transitions.
    void grid.offsetWidth;
    const transition = `transform ${duration}ms ease-out`;
    grid.style.transition = `height ${duration}ms ease-out`;
    grid.style.height = `${endHeight}px`;
    next.querySelectorAll(SELECTORS.ANIMKEY).forEach((element) => {
        if (moved.includes(element)) {
            setTransform(element, '', transition);
        }
    });
    oldElements.forEach((old) => {
        if (old.target) {
            setTransform(old.element, old.target, transition);
        }
    });

    const timer = setTimeout(() => finishSwitch(), duration);
    finishSwitch = () => {
        clearTimeout(timer);
        finishSwitch = null;
        current.hidden = true;
        current.style.height = '';
        current.style.animationDuration = '';
        current.classList.remove(CLASSES.LEAVING);
        grid.classList.remove(CLASSES.SWITCHING);
        grid.style.height = '';
        grid.style.transition = '';
        moved.forEach((element) => setTransform(element, '', ''));
        oldElements.forEach((old) => {
            old.element.style.visibility = '';
        });
        faded.forEach((element) => {
            element.classList.remove(CLASSES.FADEIN);
            element.style.animationDuration = '';
        });
    };
};

/**
 * Returns the entries of the left column.
 *
 * @param {HTMLElement} root
 * @returns {HTMLElement[]}
 */
const getNavItems = (root) => [...root.querySelectorAll(SELECTORS.NAVITEM)];

/**
 * Keyboard: only the selected entry of the left column is in the tab order, like the tabs pattern of WAI-ARIA.
 * Tab then goes straight into the panel, the arrow keys move between the entries.
 *
 * @param {HTMLElement} root
 */
const updateTabindex = (root) => {
    const items = getNavItems(root);
    const selected = items.find((item) => item.classList.contains(CLASSES.SELECTED)) || items[0];
    items.forEach((item) => {
        item.tabIndex = item === selected ? 0 : -1;
    });
};

/**
 * Returns the panel that is shown.
 *
 * @param {HTMLElement} root
 * @returns {HTMLElement|null}
 */
const getActivePanel = (root) => [...root.querySelectorAll(SELECTORS.PANEL)]
    .find((panel) => !panel.hidden && !panel.classList.contains(CLASSES.LEAVING)) || null;

/**
 * Moves the focus into the panel that is shown: to its heading, or to its first link.
 *
 * @param {HTMLElement} root
 */
const focusPanel = (root) => {
    const panel = getActivePanel(root);
    if (!panel) {
        return;
    }
    const target = panel.querySelector(SELECTORS.HEADING) || panel.querySelector(SELECTORS.FOCUSABLE);
    target?.focus();
};

/**
 * Moves the focus to an entry of the left column, selecting it if it is a community.
 *
 * @param {HTMLElement} root
 * @param {HTMLElement} item
 */
const focusNavItem = (root, item) => {
    if (item.matches(SELECTORS.SELECT)) {
        selectTab(root, item.dataset.tab, true);
    }
    item.focus();
};

/**
 * Shows the panel of a tab.
 *
 * @param {HTMLElement} root
 * @param {string} tab The tab id.
 * @param {boolean} animate Whether to animate the change.
 */
const selectTab = (root, tab, animate) => {
    if (finishSwitch) {
        finishSwitch();
    }

    root.querySelectorAll(SELECTORS.SELECT).forEach((button) => {
        const selected = button.dataset.tab === tab;
        button.classList.toggle(CLASSES.SELECTED, selected);
        button.setAttribute('aria-expanded', selected ? 'true' : 'false');
    });
    updateTabindex(root);

    const panels = [...root.querySelectorAll(SELECTORS.PANEL)];
    const next = panels.find((panel) => panel.dataset.tab === tab);
    const current = panels.find((panel) => !panel.hidden);
    if (!next || next === current) {
        return;
    }

    const grid = root.querySelector(SELECTORS.GRID);
    if (animate && current && grid && !reducedMotion()) {
        animateSwitch(grid, current, next);
        return;
    }
    panels.forEach((panel) => {
        panel.hidden = panel !== next;
    });
};

/**
 * Shows the default panel again.
 *
 * @param {HTMLElement} root
 */
const resetTabs = (root) => {
    const panel = root.querySelector(`${SELECTORS.PANEL}[data-default="true"]`);
    if (panel) {
        selectTab(root, panel.dataset.tab, false);
    }
};

/**
 * Opens the mega menu.
 *
 * @param {boolean} focusMenu Whether to move the focus to the selected entry, e.g. when opened with the keyboard.
 */
const open = (focusMenu = false) => {
    const root = getRoot();
    if (!root || isOpen()) {
        return;
    }
    clearTimeout(closeTimer);
    resetTabs(root);
    updateTabindex(root);
    root.hidden = false;
    // Let the browser render the hidden state first, so the fade in runs.
    void root.offsetWidth;
    root.classList.add(CLASSES.SHOW);
    document.body.classList.add(CLASSES.OPEN);
    getToggles().forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'true');
        toggle.closest('li')?.classList.add(CLASSES.OPEN);
    });
    if (focusMenu) {
        getNavItems(root).find((item) => item.tabIndex === 0)?.focus();
    }
};

/**
 * Closes the mega menu.
 *
 * @param {boolean} returnFocus Whether to move the focus back to the trigger.
 */
const close = (returnFocus = false) => {
    const root = getRoot();
    // The preview in the editor stays open.
    if (!root || !isOpen() || root.classList.contains(CLASSES.PREVIEW)) {
        return;
    }
    root.classList.remove(CLASSES.SHOW);
    document.body.classList.remove(CLASSES.OPEN);
    getToggles().forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.closest('li')?.classList.remove(CLASSES.OPEN);
    });
    closeTimer = setTimeout(() => {
        if (finishSwitch) {
            finishSwitch();
        }
        root.hidden = true;
    }, FADEDURATION);

    if (returnFocus) {
        const toggle = getToggles().find((element) => element.offsetParent !== null);
        toggle?.focus();
    }
};

/**
 * Shows a level of the mobile mega menu: the list (level 1) or the panel of a community.
 *
 * Both are in the same grid cell of the mobile root, so the change can run like the Smart Animate
 * of the screen design: the old level fades out, the new one fades in, the height follows.
 * The drawer scrolls back to the top, like the prototype.
 *
 * @param {HTMLElement} mobileRoot
 * @param {HTMLElement} next The level to show.
 * @param {boolean} animate Whether to animate the change.
 */
const showMobileLevel = (mobileRoot, next, animate) => {
    if (finishSwitch) {
        finishSwitch();
    }
    const levels = [mobileRoot.querySelector(SELECTORS.MOBILE_LEVEL1), ...mobileRoot.querySelectorAll(SELECTORS.MOBILE_PANEL)]
        .filter((level) => !!level);
    const current = levels.find((level) => !level.hidden);
    const isPanel = next.matches(SELECTORS.MOBILE_PANEL);

    mobileRoot.querySelectorAll(SELECTORS.MOBILE_DRILL).forEach((row) => {
        row.setAttribute('aria-expanded', isPanel && row.dataset.tab === next.dataset.tab ? 'true' : 'false');
    });
    mobileRoot.classList.toggle(CLASSES.MOBILE_PANELOPEN, isPanel);

    const scroller = mobileRoot.closest(SELECTORS.DRAWER_CONTENT);
    if (scroller) {
        scroller.scrollTop = 0;
    }

    if (next === current) {
        return;
    }
    if (animate && current && !reducedMotion()) {
        animateSwitch(mobileRoot, current, next, DRILLDURATION);
        return;
    }
    levels.forEach((level) => {
        level.hidden = level !== next;
    });
};

/**
 * Opens the panel of a community in the mobile drawer.
 *
 * @param {HTMLElement} mobileRoot
 * @param {HTMLElement} row The row that was activated.
 */
const drillDown = (mobileRoot, row) => {
    const panel = [...mobileRoot.querySelectorAll(SELECTORS.MOBILE_PANEL)]
        .find((element) => element.dataset.tab === row.dataset.tab);
    if (!panel) {
        return;
    }
    mobileOpener = row;
    showMobileLevel(mobileRoot, panel, true);
    panel.querySelector(SELECTORS.MOBILE_BACK)?.focus({preventScroll: true});
};

/**
 * Goes back to the list in the mobile drawer.
 *
 * @param {HTMLElement} mobileRoot
 * @param {boolean} animate Whether to animate the change.
 */
const drillUp = (mobileRoot, animate) => {
    const level1 = mobileRoot.querySelector(SELECTORS.MOBILE_LEVEL1);
    if (!level1) {
        return;
    }
    showMobileLevel(mobileRoot, level1, animate);
    if (animate && mobileOpener) {
        mobileOpener.focus({preventScroll: true});
    }
    mobileOpener = null;
};

/**
 * Handles clicks anywhere on the page.
 *
 * @param {MouseEvent} e
 */
const handleClick = (e) => {
    const toggle = e.target.closest(SELECTORS.TOGGLE);
    if (toggle) {
        // Modified clicks keep the normal link behaviour, e.g. opening the catalogue in a new tab.
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) {
            return;
        }
        e.preventDefault();
        if (isOpen()) {
            close();
        } else {
            // A click without a pointer comes from the keyboard (Enter or Space).
            open(e.detail === 0);
        }
        return;
    }

    const mobileRoot = getMobileRoot();
    if (mobileRoot) {
        const drill = e.target.closest(SELECTORS.MOBILE_DRILL);
        if (drill && mobileRoot.contains(drill)) {
            drillDown(mobileRoot, drill);
            return;
        }
        const back = e.target.closest(SELECTORS.MOBILE_BACK);
        if (back && mobileRoot.contains(back)) {
            drillUp(mobileRoot, true);
            return;
        }
    }

    const root = getRoot();
    if (!root || !isOpen()) {
        return;
    }

    const select = e.target.closest(SELECTORS.SELECT);
    if (select && root.contains(select)) {
        selectTab(root, select.dataset.tab, true);
        return;
    }

    if (!root.contains(e.target)) {
        close();
    }
};

/**
 * Keyboard support of the desktop menu.
 *
 * - Arrow down on the trigger opens the menu and focuses the selected entry.
 * - In the left column: arrow up/down move between the entries (a community is shown right away), Home/End
 *   jump to the first/last entry, Enter/Space/arrow right move into the panel.
 * - In the panel: arrow left goes back to the selected entry.
 * - Escape closes and returns the focus to the trigger.
 *
 * @param {KeyboardEvent} e
 */
const handleKeydown = (e) => {
    if (e.target.closest(SELECTORS.TOGGLE) && e.key === 'ArrowDown') {
        e.preventDefault();
        if (isOpen()) {
            getNavItems(getRoot()).find((item) => item.tabIndex === 0)?.focus();
        } else {
            open(true);
        }
        return;
    }
    if (!isOpen()) {
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault();
        close(true);
        return;
    }

    const root = getRoot();
    const navItem = e.target.closest(SELECTORS.NAVITEM);
    if (navItem && root.contains(navItem)) {
        const items = getNavItems(root);
        const index = items.indexOf(navItem);
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                focusNavItem(root, items[(index + 1) % items.length]);
                break;
            case 'ArrowUp':
                e.preventDefault();
                focusNavItem(root, items[(index - 1 + items.length) % items.length]);
                break;
            case 'Home':
                e.preventDefault();
                focusNavItem(root, items[0]);
                break;
            case 'End':
                e.preventDefault();
                focusNavItem(root, items[items.length - 1]);
                break;
            case 'ArrowRight':
                e.preventDefault();
                focusPanel(root);
                break;
            case 'Enter':
            case ' ':
                if (navItem.matches(SELECTORS.SELECT)) {
                    e.preventDefault();
                    selectTab(root, navItem.dataset.tab, true);
                    focusPanel(root);
                }
                break;
        }
        return;
    }

    const panel = e.target.closest(SELECTORS.PANEL);
    if (panel && root.contains(panel) && e.key === 'ArrowLeft') {
        e.preventDefault();
        (root.querySelector(`${SELECTORS.SELECT}.${CLASSES.SELECTED}`) || getNavItems(root)[0])?.focus();
    }
};

/**
 * Closes when the keyboard focus leaves the mega menu and its trigger.
 *
 * @param {FocusEvent} e
 */
const handleFocusin = (e) => {
    const root = getRoot();
    if (!root || !isOpen()) {
        return;
    }
    if (!root.contains(e.target) && !e.target.closest(SELECTORS.TOGGLE)) {
        close();
    }
};

/**
 * Shows the list again when the drawer closes, so it opens on level 1 next time.
 */
const handleDrawerHidden = () => {
    const mobileRoot = getMobileRoot();
    if (mobileRoot) {
        drillUp(mobileRoot, false);
    }
};

/**
 * Shows the panel of a tab with the switch animation, e.g. for the editor preview.
 *
 * @param {string} tab The tab id.
 */
export const showTab = (tab) => {
    const root = getRoot();
    if (root) {
        selectTab(root, tab, true);
    }
};

/**
 * Shows a community in the mobile mega menu, or the list for any other tab, e.g. for the editor preview.
 *
 * @param {string} tab The tab id.
 */
export const showMobileTab = (tab) => {
    const mobileRoot = getMobileRoot();
    if (!mobileRoot) {
        return;
    }
    const panel = [...mobileRoot.querySelectorAll(SELECTORS.MOBILE_PANEL)]
        .find((element) => element.dataset.tab === tab);
    if (panel) {
        mobileOpener = mobileRoot.querySelector(`${SELECTORS.MOBILE_DRILL}[data-tab="${tab}"]`);
        showMobileLevel(mobileRoot, panel, true);
    } else {
        drillUp(mobileRoot, true);
    }
};

/**
 * Initialises the mega menu.
 */
export const init = () => {
    if (initialised || (!getRoot() && !getMobileRoot())) {
        return;
    }
    initialised = true;

    document.addEventListener('click', handleClick);
    document.addEventListener('keydown', handleKeydown);
    document.addEventListener('focusin', handleFocusin);
    document.addEventListener('theme_boost/drawers:hidden', handleDrawerHidden);
};
