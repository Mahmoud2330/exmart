import { useState } from "react";
import { EmInput, EmSelect, Breadcrumb } from "../components/em";

const SUBJECTS = [
  "Order issue",
  "Product inquiry",
  "Authenticity / warranty",
  "Partnership",
  "Complaint",
  "Other",
];

export default function Contact() {
  const [sent, setSent] = useState(false);
  const [form, setForm] = useState({ name: "", email: "", phone: "", subject: SUBJECTS[0], message: "" });
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Contact" }]} />
      <h1 className="em-h1" style={{ marginBottom: "var(--s10)" }}>Contact Us</h1>

      <div style={{ display: "grid", gridTemplateColumns: "1fr", gap: "var(--s10)" }} className="contact-grid">
        <style>{`@media(min-width:768px){.contact-grid{grid-template-columns:1fr 1fr !important;}}`}</style>

        {/* Info */}
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s8)" }}>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Address</p>
            <p className="em-body-s" style={{ color: "var(--ink-700)" }}>12 El-Nozha Street, Heliopolis<br />Cairo, Egypt 11361</p>
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Phone & WhatsApp</p>
            <a href="tel:+201001234567" style={{ display: "block", color: "var(--ink-800)", textDecoration: "none", fontWeight: 600 }}>+20 100 123 4567</a>
            <a href="https://wa.me/201001234567" style={{ display: "inline-flex", alignItems: "center", gap: "var(--s2)", color: "var(--success)", fontSize: ".875rem", fontWeight: 600, marginTop: "var(--s2)", textDecoration: "none" }}>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path fillRule="evenodd" clipRule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.89.52 3.66 1.43 5.17L2 22l4.95-1.41A9.97 9.97 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="var(--success)" />
              </svg>
              Chat on WhatsApp
            </a>
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Email</p>
            <a href="mailto:hello@exmart.eg" style={{ color: "var(--ink-800)", textDecoration: "none", fontWeight: 600 }}>hello@exmart.eg</a>
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Hours</p>
            <p className="em-body-s" style={{ color: "var(--ink-700)" }}>
              Sunday – Thursday: 9:00 am – 6:00 pm<br />
              Friday – Saturday: 10:00 am – 3:00 pm
            </p>
          </div>

          {/* Map placeholder */}
          <div style={{ borderRadius: "var(--r-md)", overflow: "hidden", border: "1px solid var(--ink-200)", aspectRatio: "16/9", background: "var(--ink-100)", display: "flex", alignItems: "center", justifyContent: "center" }}>
            <div style={{ textAlign: "center", display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s2)" }}>
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="var(--ink-400)" strokeWidth="1.5" />
                <circle cx="12" cy="9" r="2.5" stroke="var(--ink-400)" strokeWidth="1.5" />
              </svg>
              <p className="em-caption" style={{ color: "var(--ink-400)" }}>12 El-Nozha Street, Heliopolis, Cairo</p>
            </div>
          </div>
        </div>

        {/* Form */}
        {sent ? (
          <div style={{ display: "flex", flexDirection: "column", alignItems: "flex-start", gap: "var(--s4)", paddingTop: "var(--s8)" }}>
            <div style={{ width: 56, height: 56, borderRadius: "50%", background: "var(--success-bg)", display: "flex", alignItems: "center", justifyContent: "center" }}>
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12l4 4 10-10" stroke="var(--success)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
            </div>
            <h2 className="em-h3">Message sent!</h2>
            <p className="em-body" style={{ color: "var(--ink-500)" }}>Thank you for reaching out. We will reply to {form.email} within one business day.</p>
            <button className="em-btn em-btn-secondary" onClick={() => setSent(false)}>Send another message</button>
          </div>
        ) : (
          <form
            onSubmit={(e) => { e.preventDefault(); setSent(true); }}
            style={{ display: "flex", flexDirection: "column", gap: "var(--s4)" }}
          >
            <div className="em-form-row">
              <EmInput label="Full name" id="c-name" value={form.name} onChange={(e) => set("name", e.target.value)} required />
              <EmInput label="Email address" id="c-email" type="email" value={form.email} onChange={(e) => set("email", e.target.value)} required />
            </div>
            <EmInput label="Phone (optional)" id="c-phone" type="tel" value={form.phone} onChange={(e) => set("phone", e.target.value)} />
            <EmSelect label="Subject" id="c-subject" options={SUBJECTS} value={form.subject} onChange={(e) => set("subject", e.target.value)} />
            <div className="em-input-wrap">
              <label className="em-input-label" htmlFor="c-message">Message</label>
              <textarea
                id="c-message"
                className="em-input"
                rows={6}
                style={{ resize: "vertical", minHeight: 120 }}
                value={form.message}
                onChange={(e) => set("message", e.target.value)}
                required
                placeholder="Describe your enquiry in detail…"
              />
            </div>
            <button type="submit" className="em-btn em-btn-primary em-btn-lg" style={{ alignSelf: "flex-start", minWidth: 180 }}>
              Send message
            </button>
          </form>
        )}
      </div>
    </div>
  );
}
