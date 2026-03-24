# Stripe Checkout Flow

This project now supports a full Stripe Checkout flow for payments.

## Implemented endpoints

- `POST /api/checkout/create-session`
- `GET /checkout/success`
- `GET /checkout/cancel`
- `POST /api/stripe/webhook`

## Environment variables

Set these values in `.env`:

- `APP_URL`
- `STRIPE_SECRET_KEY`
- `STRIPE_PUBLISHABLE_KEY`
- `STRIPE_WEBHOOK_SECRET`

## Database migration

Run `Database/Migrations/migration-v20-stripe-checkout-flow.sql`.

## Local webhook forwarding

Use Stripe CLI to forward events:

```bash
stripe listen --forward-to http://localhost/api/stripe/webhook
```

Copy the generated signing secret into `STRIPE_WEBHOOK_SECRET`.

## Test flow

1. Log in.
2. Add tickets to `/my-program`.
3. Open `/checkout` and submit payment.
4. Complete test payment on Stripe.
5. Verify that:
   - `Order.Status` becomes `Paid`
   - `Payment.Status` becomes `Paid`
   - `Program.IsCheckedOut` becomes `1`
   - event id is stored in `StripeWebhookEvent`

