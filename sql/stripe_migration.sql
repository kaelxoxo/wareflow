-- Run this once against your wareflow database to add Stripe subscription columns.
-- Safe to run on an existing database; uses IF NOT EXISTS / column checks.

ALTER TABLE tenants
  ADD COLUMN IF NOT EXISTS stripe_customer_id       VARCHAR(100) NULL          AFTER plan,
  ADD COLUMN IF NOT EXISTS stripe_subscription_id   VARCHAR(100) NULL          AFTER stripe_customer_id,
  ADD COLUMN IF NOT EXISTS subscription_status      ENUM('none','trialing','active','past_due','canceled','unpaid')
                                                    NOT NULL DEFAULT 'none'    AFTER stripe_subscription_id,
  ADD COLUMN IF NOT EXISTS subscription_period_end  TIMESTAMP    NULL          AFTER subscription_status;
