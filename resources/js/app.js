import './bootstrap';
import Alpine from 'alpinejs';

// ============================================================
// Unit Conversion System
// ============================================================
const conversions = {
    // Volume: imperial -> metric
    cup:    { to: 'ml',  factor: 236.588 },
    tbsp:   { to: 'ml',  factor: 14.787 },
    tsp:    { to: 'ml',  factor: 4.929 },
    'fl oz':{ to: 'ml',  factor: 29.574 },
    // Weight: imperial -> metric
    oz:     { to: 'g',   factor: 28.3495 },
    lb:     { to: 'g',   factor: 453.592 },
    // Volume: metric -> imperial
    ml:     { to: 'fl oz', factor: 1 / 29.574 },
    l:      { to: 'cup',   factor: 4.22675 },
    // Weight: metric -> imperial
    g:      { to: 'oz',  factor: 1 / 28.3495 },
    kg:     { to: 'lb',  factor: 2.20462 },
};

// Reverse map for toggling back
const reverseConversions = {};
for (const [unit, conv] of Object.entries(conversions)) {
    reverseConversions[conv.to] = reverseConversions[conv.to] || { to: unit, factor: 1 / conv.factor };
}

function smartRound(value) {
    if (value >= 100) return Math.round(value);
    if (value >= 10) return Math.round(value * 2) / 2; // round to 0.5
    if (value >= 1) return Math.round(value * 4) / 4;  // round to 0.25
    return Math.round(value * 100) / 100;
}

function formatAmount(value) {
    if (value === null || value === undefined) return '';
    const rounded = smartRound(value);
    // Clean trailing zeros
    return parseFloat(rounded.toFixed(2)).toString();
}

function convertUnit(amount, unit, targetSystem) {
    if (!amount || !unit) return { amount, unit };

    const isMetricUnit = ['g', 'kg', 'ml', 'l'].includes(unit);
    const isImperialUnit = ['cup', 'tbsp', 'tsp', 'oz', 'lb', 'fl oz'].includes(unit);

    // Already in target system
    if (targetSystem === 'metric' && isMetricUnit) return { amount, unit };
    if (targetSystem === 'imperial' && isImperialUnit) return { amount, unit };

    // Convert
    const conv = conversions[unit];
    if (!conv) return { amount, unit }; // Unknown unit, leave as-is

    return {
        amount: amount * conv.factor,
        unit: conv.to,
    };
}

// ============================================================
// Alpine Components
// ============================================================

// Global unit toggle (in navbar)
Alpine.data('unitToggle', () => ({
    system: localStorage.getItem('unitSystem') || 'metric',
    toggle() {
        this.system = this.system === 'metric' ? 'imperial' : 'metric';
        localStorage.setItem('unitSystem', this.system);
        // Dispatch event so recipe pages update
        window.dispatchEvent(new CustomEvent('unit-system-changed', { detail: this.system }));
    }
}));

// Unit converter on recipe show page
Alpine.data('unitConverter', () => ({
    system: localStorage.getItem('unitSystem') || 'metric',

    init() {
        window.addEventListener('unit-system-changed', (e) => {
            this.system = e.detail;
        });
    },

    toggleSystem() {
        this.system = this.system === 'metric' ? 'imperial' : 'metric';
        localStorage.setItem('unitSystem', this.system);
        window.dispatchEvent(new CustomEvent('unit-system-changed', { detail: this.system }));
    },

    convertIngredient(amount, unit, name) {
        if (!amount) {
            return unit ? `${unit} ${name}` : name;
        }

        const result = convertUnit(parseFloat(amount), unit, this.system);
        const formatted = formatAmount(result.amount);

        if (result.unit) {
            return `${formatted} ${result.unit} ${name}`;
        }
        return `${formatted} ${name}`;
    },
}));

// Vote button
Alpine.data('voteButton', (recipeId, initialVoted, initialCount) => ({
    voted: initialVoted,
    count: initialCount,
    loading: false,

    async toggle() {
        if (this.loading) return;
        this.loading = true;

        // Optimistic update
        this.voted = !this.voted;
        this.count += this.voted ? 1 : -1;

        try {
            const response = await fetch(`/recipes/${recipeId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                this.voted = data.voted;
                this.count = data.count;
            } else {
                // Revert optimistic update
                this.voted = !this.voted;
                this.count += this.voted ? 1 : -1;
            }
        } catch {
            this.voted = !this.voted;
            this.count += this.voted ? 1 : -1;
        } finally {
            this.loading = false;
        }
    }
}));

// Recipe form (create/edit)
Alpine.data('recipeForm', (existingIngredients = null, existingSteps = null) => ({
    ingredients: existingIngredients || [{ amount: '', unit: '', name: '', group: '', known_ingredient_id: '' }],
    steps: existingSteps || [{ instruction: '' }],

    addIngredient() {
        this.ingredients.push({ amount: '', unit: '', name: '', group: '', known_ingredient_id: '' });
    },

    removeIngredient(index) {
        this.ingredients.splice(index, 1);
    },

    addStep() {
        this.steps.push({ instruction: '' });
    },

    removeStep(index) {
        this.steps.splice(index, 1);
    },
}));

// Ingredient autocomplete for individual ingredient rows
Alpine.data('ingredientAutocomplete', (ingredient) => ({
    query: ingredient.name || '',
    suggestions: [],
    showSuggestions: false,
    debounceTimer: null,
    selectedId: ingredient.known_ingredient_id || '',

    init() {
        this.$watch('query', (val) => {
            // Update parent ingredient name
            ingredient.name = val;
            // Clear the linked known_ingredient if user is typing something new
            if (this.selectedId && val !== this._lastSelectedName) {
                this.selectedId = '';
                ingredient.known_ingredient_id = '';
            }
            this.debouncedSearch(val);
        });
    },

    debouncedSearch(val) {
        clearTimeout(this.debounceTimer);
        if (val.length < 2) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }
        this.debounceTimer = setTimeout(() => this.search(val), 250);
    },

    async search(val) {
        try {
            const res = await fetch(`/api/ingredients?q=${encodeURIComponent(val)}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (res.ok) {
                this.suggestions = await res.json();
                this.showSuggestions = this.suggestions.length > 0;
            }
        } catch { /* ignore */ }
    },

    select(suggestion) {
        this.query = suggestion.name;
        this._lastSelectedName = suggestion.name;
        this.selectedId = suggestion.id;
        ingredient.name = suggestion.name;
        ingredient.known_ingredient_id = suggestion.id;
        this.showSuggestions = false;
        this.suggestions = [];
    },

    close() {
        // Small delay so click on suggestion registers first
        setTimeout(() => { this.showSuggestions = false; }, 150);
    },
}));

window.Alpine = Alpine;
Alpine.start();
