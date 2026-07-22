---
name: form-field-validation
description:
---

# SKILL_DEFINITION: Form Field Validation

## Purpose

This skill defines the standard behavior for client-side form validation.

The goal is to prevent invalid submissions without presenting fields as erroneous before the user has had a reasonable opportunity to complete them.

Validation must distinguish between:

* A field that is technically invalid.
* A field whose validation error should currently be visible.

These are not the same state.

## Core UX principle

A required empty field is technically invalid, but it should not be presented as a user error when the form has just loaded.

Do not show red borders, error icons, or messages for untouched fields before the user interacts with them or attempts to submit the form.

Use this rule:

```ts
const shouldShowError =
    field.invalid &&
    (field.touched || form.submitAttempted);
```

Equivalent state names such as `visited`, `dirty`, `blurred`, `submitted`, or `showValidation` are acceptable when consistent with the existing application architecture.

## Validation lifecycle

Every field should support the following logical states:

### Neutral

The field has not been meaningfully interacted with.

Behavior:

* Do not show an error.
* Do not use red borders or destructive styling.
* Required status may be communicated using an asterisk, label, helper text, or semantic `required` attribute.

Example:

```text
First name *
[                               ]
```

### Invalid after interaction

The user has focused and left the field, or otherwise completed a meaningful interaction, and the current value is invalid.

Behavior:

* Show the field-level error.
* Apply invalid styling.
* Associate the error message with the input for accessibility.
* Keep the message specific and actionable.

Example:

```text
First name *
[                               ]
Please enter your first name.
```

### Invalid after submit attempt

The user attempted to submit the form while one or more fields were invalid.

Behavior:

* Mark all invalid fields as eligible to display errors.
* Focus or scroll to the first invalid field.
* Keep field-level messages visible until their corresponding values become valid.
* Do not clear valid user input.

### Valid after correction

The user corrects an invalid field.

Behavior:

* Remove the error as soon as the value becomes valid.
* Do not require another form submission to clear the message.
* Avoid showing success styling unless it provides meaningful value.

## Recommended interaction model

Use the following default behavior:

1. Validate required metadata and constraints internally from the beginning.
2. Do not display errors on initial render.
3. Mark a field as touched after `blur` or an equivalent completed interaction.
4. After the first submit attempt, display all remaining validation errors.
5. Revalidate previously invalid fields while the user edits them.
6. Remove visible errors immediately when the value becomes valid.

Recommended implementation:

```ts
type FieldState = {
    touched: boolean;
    invalid: boolean;
};

type FormState = {
    submitAttempted: boolean;
};

function shouldShowFieldError(
    field: FieldState,
    form: FormState
): boolean {
    return field.invalid && (field.touched || form.submitAttempted);
}
```

## Trigger behavior

### Before the first submit attempt

Prefer validation display on:

* `blur`
* `focusout`
* completed selection
* completed date choice
* meaningful component interaction

Avoid showing errors on every keystroke for untouched fields.

### After an error is visible

Once a field error has been displayed, revalidate on:

* `input`
* `change`
* component-specific value updates

This lets the error disappear immediately after correction.

### On submit

When submission is attempted:

```ts
form.submitAttempted = true;
validateAllFields();

if (!form.isValid) {
    focusFirstInvalidField();
    return;
}

submitForm();
```

## Required fields

Communicate required status before validation occurs.

Recommended options:

```html
<label for="first-name">
    First name <span aria-hidden="true">*</span>
</label>
<input
    id="first-name"
    name="first_name"
    required
    aria-required="true"
/>
```

An asterisk must not be the only explanation when the form contains both required and optional fields. Include a general note such as:

```text
Fields marked with * are required.
```

Alternatively, explicitly label optional fields:

```text
Company name (optional)
```

Do not display `This field is required` immediately when the form loads.

## Error messages

Error messages must:

* Explain what is wrong.
* Tell the user how to correct it.
* Be placed close to the affected field.
* Avoid technical terminology.
* Avoid blaming the user.
* Avoid exposing backend or validation-rule internals.

Prefer:

```text
Please enter your first name.
```

```text
Enter a valid email address.
```

```text
Password must contain at least 8 characters.
```

Avoid:

```text
Invalid value.
```

```text
Validation failed.
```

```text
The field does not satisfy rule required|string|max:255.
```

For required fields, prefer naming the expected information rather than repeating a generic message everywhere.

## Accessibility requirements

Each visible error must be programmatically associated with its field.

Example:

```html
<label for="email">Email address</label>

<input
    id="email"
    name="email"
    type="email"
    aria-invalid="true"
    aria-describedby="email-error"
/>

<p id="email-error" role="alert">
    Enter a valid email address.
</p>
```

Requirements:

* Set `aria-invalid="true"` only while the visible field state is invalid.
* Use `aria-describedby` to connect helper and error text.
* Do not communicate errors using color alone.
* Preserve visible focus indicators.
* Focus the first invalid field after a failed submit attempt.
* Avoid moving focus unexpectedly during normal typing.
* For dynamic errors, use `role="alert"` or an appropriate live region without causing repeated announcements on every keystroke.

## Form-level errors

Use a form-level summary when:

* The form is long.
* Errors may be outside the viewport.
* Multiple sections or tabs are involved.
* The submit action fails for reasons unrelated to one specific field.

Example:

```text
Please correct the highlighted fields before continuing.
```

A form-level summary must complement field-level messages, not replace them.

For long forms, the summary may contain links to invalid fields.

## Disabled submit buttons

Do not rely exclusively on a disabled submit button to communicate invalid state.

A permanently disabled button can leave the user unable to understand what is missing.

Preferred options:

* Keep the primary action enabled and validate on submit.
* Disable it only when the missing requirements are obvious and continuously communicated.
* For wizard flows, show a neutral message explaining what remains incomplete.

Example:

```text
Complete 1 required field to continue.
```

Do not simulate a clickable disabled button solely to reveal validation errors.

## Wizard and multi-step forms

For step-based forms:

* Validate the current step when the user attempts to continue.
* Do not reveal errors in future untouched steps.
* Preserve completed values when navigating backward.
* Mark steps with errors only after an attempt to leave or submit that step.
* Focus the first invalid field in the current step.
* Do not block unrelated optional sections.

Recommended state:

```ts
type StepState = {
    visited: boolean;
    continueAttempted: boolean;
    valid: boolean;
};
```

Error visibility may use:

```ts
const shouldShowError =
    field.invalid &&
    (field.touched || step.continueAttempted || form.submitAttempted);
```

## Server-side validation

Client-side validation improves usability but does not replace server-side validation.

Always validate submitted data on the backend.

When the backend returns validation errors:

* Map each error to its corresponding field.
* Mark the affected field as touched or server-invalid.
* Display the backend message using the same field error component.
* Focus the first relevant invalid field.
* Preserve all submitted values except secrets that must be cleared.
* Show unmatched errors in a form-level error area.

Recommended normalized error structure:

```ts
type ValidationErrors = Record<string, string[]>;

const errors: ValidationErrors = {
    email: ['This email address is already registered.'],
    password: ['Password must contain at least 8 characters.']
};
```

Do not show raw API response objects directly in the UI.

## Async validation

For checks such as username, email, coupon, or identifier availability:

* Do not run a request on every keystroke without debounce.
* Do not show an error while the value is incomplete.
* Cancel or ignore stale requests.
* Distinguish validation failure from network failure.
* Do not block submission indefinitely when the check cannot complete.
* Display a loading state only when it is useful.

Example:

```ts
const result = await validateEmailAvailability(email, {
    signal: abortController.signal
});
```

A network error should not be presented as:

```text
This email is invalid.
```

Use:

```text
We could not verify this email right now. Try again.
```

## Validation timing by field type

### Text fields

* Show required errors after blur or submit attempt.
* Revalidate while typing after an error has appeared.

### Email fields

* Do not show an invalid email message for partial initial input.
* Validate format after blur.
* After the error appears, clear it as soon as the format becomes valid.

### Password fields

* Display requirements before or during entry as neutral guidance.
* Do not initially render every unmet rule as an error.
* After interaction, indicate which requirements remain unmet.

### Select fields

* Validate after the user closes or leaves the control.
* Treat placeholder options as empty values.

### Checkboxes

* For required agreements, show the error after submit or after the user interacts with the control.
* Associate the error with the entire checkbox group when applicable.

### Radio groups

* Validate the group, not each individual radio input.
* Place one error message after the group.

### Date fields

* Distinguish missing, malformed, and out-of-range dates.
* Use localized display formats while preserving normalized internal values.

### File fields

* Explain accepted file types and limits before selection.
* Validate size, type, and quantity after selection.
* Do not clear valid selected files because another field failed.

## Reusable component contract

Reusable field components should accept enough information to separate validity from error visibility.

Example:

```ts
type FormFieldProps = {
    id: string;
    name: string;
    label: string;
    value: unknown;
    required?: boolean;
    error?: string | null;
    touched?: boolean;
    submitAttempted?: boolean;
};
```

The component should derive:

```ts
const showError =
    Boolean(error) &&
    Boolean(touched || submitAttempted);
```

Avoid components that show an error solely because an `error` property exists during initial form construction.

## Suggested CSS state

Use explicit state classes or data attributes:

```html
<div
    class="form-field"
    data-touched="true"
    data-invalid="true"
>
```

Or:

```html
<input class:is-invalid={showError} />
```

Do not infer visible validation only from CSS pseudo-classes such as `:invalid`, because browsers may apply them before user interaction.

If native constraint validation is used, combine it with interaction state:

```css
.form-submitted input:invalid,
.form-field[data-touched='true'] input:invalid {
    /* Invalid styling */
}
```

Avoid globally styling:

```css
input:invalid {
    /* Red on initial render */
}
```

## Native browser validation

Native validation may be used when it matches the application UX.

When using custom validation UI, consider adding:

```html
<form novalidate>
```

This avoids inconsistent browser popups and lets the application control:

* Error timing
* Error wording
* Focus behavior
* Localization
* Visual consistency

When `novalidate` is used, all equivalent validation and accessibility behavior must still be implemented.

## Localization

Validation messages must use the application translation system.

Do not hardcode user-facing messages inside reusable validation functions.

Prefer message keys:

```ts
const validationMessageKeys = {
    required: 'validation.required',
    email: 'validation.email',
    minLength: 'validation.min_length'
};
```

Allow field-specific messages:

```ts
translate('validation.required_field', {
    field: translate('fields.first_name')
});
```

Do not interpolate untranslated backend property names into user-facing text.

## Backend and frontend consistency

Frontend and backend constraints must remain aligned.

Examples:

* Required status
* Minimum and maximum lengths
* Accepted formats
* Date limits
* Numeric ranges
* File limits
* Uniqueness rules where practical

The backend remains authoritative.

Avoid duplicating complex domain rules across several form components. Centralize shared validation schemas or adapters when supported by the stack.

## Anti-patterns

Do not:

* Show all required-field errors on initial render.
* Apply red borders to untouched fields.
* Treat an empty initial field as a user mistake.
* Validate every field aggressively on every keystroke from the start.
* Keep an error visible after the value becomes valid.
* Disable submission without explaining what remains incomplete.
* Use placeholder text as the only label.
* Use color as the only error indicator.
* Replace specific field messages with one generic toast.
* Clear the entire form after a validation failure.
* Expose raw backend validation payloads.
* Focus multiple fields or repeatedly steal focus.
* Use native `:invalid` styling without interaction-state control.

## Implementation checklist

Before considering a form complete, verify:

* Untouched required fields are neutral on initial render.
* Required fields are identifiable before submission.
* Field errors appear after blur or submit attempt.
* Submit attempts reveal all relevant errors.
* The first invalid field receives focus when appropriate.
* Errors disappear immediately after correction.
* Error messages are specific and localized.
* Error messages are associated through ARIA attributes.
* Validation is not communicated through color alone.
* Server validation errors use the same visual system.
* User input is preserved after validation failure.
* Multi-step forms validate only the relevant step.
* Automated tests cover initial, touched, submitted, corrected, and backend-error states.

## Minimum test cases

Every reusable form implementation should test at least:

```text
1. Initial render does not show required-field errors.
2. Leaving a required field empty displays its error.
3. Typing a valid value removes the visible error.
4. Submitting an untouched invalid form displays all relevant errors.
5. Submission focuses the first invalid field.
6. Valid input is preserved when another field is invalid.
7. Server validation errors are mapped to the correct fields.
8. Error messages are accessible through aria-describedby.
9. The form submits only when all required constraints are valid.
```

## Default decision

When no project-specific behavior has been defined, use:

```ts
showError = invalid && (touched || submitAttempted);
```

This is the default UX standard for required fields and other synchronous validation errors.
