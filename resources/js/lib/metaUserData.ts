/**
 * User-data parameters fed to the Meta Pixel browser SDK for manual advanced
 * matching. Values are passed plain — the Pixel hashes them with SHA-256
 * before forwarding to Meta. Mirrors the server-side normalization done by
 * `app/Services/Meta/MetaPixelUserDataFactory.php` so that browser and CAPI
 * events end up with identical hashes (which is what makes the Event Match
 * Quality score improve).
 */
export interface MetaUserDataInput {
    email?: string | null;
    phone?: string | null;
    firstName?: string | null;
    lastName?: string | null;
    externalId?: string | null;
    country?: string | null;
}

export interface MetaUserData {
    em?: string;
    ph?: string;
    fn?: string;
    ln?: string;
    external_id?: string;
    country?: string;
}

const DEFAULT_PHONE_COUNTRY_CODE = '39';

function normalizeEmail(value: string): string {
    return value.trim().toLowerCase();
}

function normalizeName(value: string): string {
    return value
        .trim()
        .toLowerCase()
        .replace(/[^\p{L}\s]+/gu, '')
        .replace(/\s+/gu, ' ')
        .trim();
}

function normalizePhone(value: string): string {
    let digits = value.replace(/\D+/g, '');

    if (digits.startsWith('00')) {
        digits = digits.slice(2);
    }

    if (digits.startsWith(DEFAULT_PHONE_COUNTRY_CODE)) {
        return digits;
    }

    if (digits.length >= 9 && digits.length <= 10) {
        return `${DEFAULT_PHONE_COUNTRY_CODE}${digits}`;
    }

    return digits;
}

function normalizeCountry(value: string): string {
    return value.replace(/[^A-Za-z]/g, '').slice(0, 2).toLowerCase();
}

export function buildMetaUserData(input: MetaUserDataInput): MetaUserData {
    const userData: MetaUserData = {};

    if (input.email) {
        const em = normalizeEmail(input.email);
        if (em) {
            userData.em = em;
        }
    }

    if (input.phone) {
        const ph = normalizePhone(input.phone);
        if (ph) {
            userData.ph = ph;
        }
    }

    if (input.firstName) {
        const fn = normalizeName(input.firstName);
        if (fn) {
            userData.fn = fn;
        }
    }

    if (input.lastName) {
        const ln = normalizeName(input.lastName);
        if (ln) {
            userData.ln = ln;
        }
    }

    if (input.externalId) {
        userData.external_id = input.externalId;
    }

    if (input.country) {
        const country = normalizeCountry(input.country);
        if (country) {
            userData.country = country;
        }
    }

    return userData;
}
