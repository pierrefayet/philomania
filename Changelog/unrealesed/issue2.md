### Change and Added
- Change controllers and navigation layout for dynamic content and admin features

### Navigation
- Refactored navigation layout to display on two lines:
    - First line: Public links (Accueil, Qui suis-je ?, Contact).
    - Second line: Dynamic links based on user connection and roles:
        - For connected users: Display email and admin-specific links (Theme, Form-Theme, Form-Synthese).
        - For non-connected users: Display Connexion and Créer un compte links.
    - Used Tailwind CSS classes for layout:
        - `flex flex-col` for vertical layout.
        - `space-y-4` for spacing between lines.
        - `space-x-6` for horizontal link spacing.

### ThemeController
- Implemented logic to dynamically retrieve and render themes:
    - `CgetTheme`: Fetches all themes and passes them to the template.
    - `getTheme`: Fetches a single theme by ID.
    - `postTheme`: Handles theme creation and assigns the current user.
    - `patchTheme`: Updates existing themes with new data.
    - `deleteTheme`: Deletes a theme after validating CSRF token.

### SynthesisController
- Added functionality to manage syntheses dynamically:
    - `CgetSynthesis`: Fetches all syntheses.
    - `getSynthesis`: Displays a specific synthesis by ID.
    - `postSynthesis`: Creates a synthesis and associates it with a theme. Sets `isActive` to `false` when a synthesis is attached.
    - `patchSynthesis`: Updates an existing synthesis.
    - `deleteSynthesis`: Deletes a synthesis after CSRF validation.

### Theme Entity
- Enhanced logic in the `Theme` entity:
    - `isActive` is now automatically set to `false` when a `Synthesis` is attached using the `setSynthesis` method.
    - Added a lifecycle callback for `updatedAt` to update timestamps on changes.

### Twig Templates
- Updated templates to dynamically display content:
    - `daily_theme.html.twig`:
        - Displays active theme and its synthesis if available.
        - Shows fallback messages when no active theme or synthesis is set.
    - `themes.html.twig`:
        - Lists all themes with options to view details, update, or delete.
    - Adjusted navigation links to handle dynamic user states and roles.

