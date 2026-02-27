# Archery Stats Management System — User Manual

**Version:** 1.1
**Last Updated:** February 2026
**Live URL:** https://sportdns.com/laravel/public/

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [User Roles](#2-user-roles)
3. [Getting Started](#3-getting-started)
   - 3.1 [Registration](#31-registration)
   - 3.2 [Login](#32-login)
   - 3.3 [Forgot Password](#33-forgot-password)
4. [Navigation](#4-navigation)
5. [Module: Archers](#5-module-archers)
   - 5.1 [Archer List](#51-archer-list)
   - 5.2 [Adding an Archer](#52-adding-an-archer)
   - 5.3 [Archer Profile](#53-archer-profile)
   - 5.4 [Editing Archer Information](#54-editing-archer-information)
   - 5.5 [Personal Achievements](#55-personal-achievements)
   - 5.6 [Performance Analytics](#56-performance-analytics)
6. [Module: Coaches](#6-module-coaches)
   - 6.1 [Coach List](#61-coach-list)
   - 6.2 [Adding a Coach](#62-adding-a-coach)
   - 6.3 [Coach Profile](#63-coach-profile)
   - 6.4 [Assigning Archers to a Coach](#64-assigning-archers-to-a-coach)
   - 6.5 [Training Sessions](#65-training-sessions)
7. [Module: Sessions & Scorecards](#7-module-sessions--scorecards)
   - 7.1 [Starting a New Session](#71-starting-a-new-session)
   - 7.2 [Round Types](#72-round-types)
   - 7.3 [Entering Scores](#73-entering-scores)
   - 7.4 [Scoring Systems](#74-scoring-systems)
   - 7.5 [Viewing Session History](#75-viewing-session-history)
8. [Module: Clubs](#8-module-clubs)
   - 8.1 [Creating a Club](#81-creating-a-club)
   - 8.2 [Club Profile & Edit](#82-club-profile--edit)
   - 8.3 [Club Dashboard](#83-club-dashboard)
   - 8.4 [Managing Members](#84-managing-members)
   - 8.5 [Membership Invitations](#85-membership-invitations)
9. [Module: Admin Settings](#9-module-admin-settings)
10. [Role Permissions Summary](#10-role-permissions-summary)
11. [Frequently Asked Questions](#11-frequently-asked-questions)

---

## 1. Introduction

The **Archery Stats Management System** is a web-based platform for managing archers, coaches, clubs, and performance data. It supports multi-role access — super administrators, club administrators, coaches, and archers each have a tailored experience suited to their needs.

**Key features:**
- Archer and coach profile management
- Live scorecard entry with WA-compliant round types
- Performance analytics with charts and trend analysis
- Club management with dashboard and member invitations
- Personal achievement records

---

## 2. User Roles

| Role | Description |
|------|-------------|
| **Super Admin** | Full access to all modules. Can create/delete archers, coaches, clubs, and manage system settings. |
| **Club Admin** | Manages their own club — can view/edit club info, invite/remove archers and coaches, and view the club performance dashboard. |
| **Coach** | Can view assigned archers, log training sessions, and view performance data for their archers. |
| **Archer** | Can view and edit their own profile, enter scores, and manage their personal achievements. |

> **Default admin login:** `admin@archery.my` / `password`

---

## 3. Getting Started

### 3.1 Registration

New users can register at `/register`. Three account types are available:

**Archer**
- Select the "Archer" card
- Fill in: Full Name, Email, Password
- You are logged in immediately and redirected to your archer profile to complete it

**Coach**
- Select the "Coach" card
- Fill in: Full Name, Email, Password
- You are logged in immediately and redirected to your coach profile to complete it

**Club (Club Administrator)**
- Select the "Club" card
- An additional **Club Name** field appears (required)
- Fill in: Club Name, Full Name (admin name), Email, Password
- A new club record is created and linked to your account automatically

> **No email verification is required.** You are logged in and taken to your profile straight after registering.

> **Note:** If a role button is grayed out and labelled **"Suspended"**, the administrator has closed registration for that account type. Contact your system administrator for assistance.

### 3.2 Login

Go to `/login` and enter your registered email and password.

- Check **"Remember me"** to stay logged in on this device
- Upon login, you are redirected based on your role:
  - **Super Admin / Club Admin** → Archers list or Club Dashboard
  - **Coach** → Coach profile
  - **Archer** → Own archer profile

### 3.3 Forgot Password

1. On the login page, click **"Forgot password?"**
2. Enter your registered email address and submit
3. A password reset link is sent to your email
4. Click the link and enter a new password (minimum 8 characters)
5. You are redirected to login with a success message

---

## 4. Navigation

The left sidebar provides navigation based on your role:

| Sidebar Item | Visible To | Description |
|---|---|---|
| **Archers** | Super Admin, Club Admin, Coach | List of all archers |
| **Coaches** | Super Admin, Club Admin, Coach | List of all coaches |
| **Clubs** | Super Admin | List of all clubs |
| **My Club** | Club Admin | Dashboard for own club |
| **Settings** | Super Admin | System settings |

On mobile, tap the **☰ hamburger** button at the top-left to open the sidebar. Tap the overlay or the close button to dismiss it.

---

## 5. Module: Archers

### 5.1 Archer List

**URL:** `/archers`
**Access:** Super Admin, Club Admin, Coach

Displays a table of all registered archers with:
- Ref No (e.g. ARCH-00001)
- Name and email
- Gender badge
- Division(s)
- State
- Club
- Action buttons: **View**, **Edit**, **Delete**

A stats bar at the top shows: Total · Male · Female · Showing (current page).

Use the search or filter (if available) to find specific archers.

### 5.2 Adding an Archer

**Access:** Super Admin, Club Admin only

1. Click **+ ADD ARCHER** (top-right of archer list)
2. Fill in the required fields:
   - **Full Name** (required)
   - **Email** (required — used for login)
   - **Date of Birth**, **Gender**
   - **Division(s)** — select one or more (Recurve, Compound, Barebow, etc.)
   - **Classification** *(required)* — **U12**, **U15**, **U18**, or **Open**
   - **Club** — optional; select from dropdown or type a new name
   - **State / National Team**
   - **Equipment** — arrow and limb specifications
   - **Personal Best scores** — unofficial and official records
   - **Photo** — JPG/PNG/WebP, max 2MB
3. Click **SAVE ARCHER**

The system auto-generates a reference number (e.g. `ARCH-00001`).

> **Classification is required** when creating or editing an archer profile. Choose the age-group category that matches the archer's competition class: U12, U15, U18, or Open.

### 5.3 Archer Profile

**URL:** `/archers/{id}`

The profile page displays:
- **Photo** and identity card (ref no, active status)
- **Divisions** and **Classification** badges
- **Personal Information** — DOB, age, gender, phone, club, state/national team
- **Location** — address, postcode, state, country
- **Equipment** — arrow and limb details
- **Personal Best** — unofficial (training) and official scores
- **Notes**
- **Recent Sessions** — last 3 sessions with scores
- **Performance Summary** — total sessions, best score, 30-day average, avg score per arrow
- **Personal Achievements** — tournament medals and records

Action buttons (top-right):
- **NEW SESSION** — start a new scoring session
- **EDIT** — edit profile (visible to owner and admins)
- **DELETE** — permanently delete archer (Super Admin only)

### 5.4 Editing Archer Information

**Access:** The archer themselves, Club Admin, Super Admin

1. Open the archer profile and click **EDIT**
2. Update any fields across the sections: Basic Info, Contact, Location, Equipment, Classification, Personal Best, Notes, Photo
3. Click **SAVE CHANGES**

> Archers can only edit their **own** profile. They cannot edit other archers' profiles.

### 5.5 Personal Achievements

Located at the bottom of the archer profile page.

**Adding an achievement:**
1. Click **+ Add Achievement** (top-right of the section)
2. A form slides open with four fields:
   - **Date** (required)
   - **Achievement** — e.g. "Gold Medal — Individual Recurve" (required)
   - **Team** — e.g. "Malaysia", "Selangor"
   - **Tournament** — e.g. "SEA Games 2025", "MSN Cup"
3. Click **SAVE ACHIEVEMENT**

**Removing an achievement:**
- Click **Remove** on any row (confirmation dialog appears)

> Archers can manage their own achievements. Club Admins and Super Admins can manage any archer's achievements.

### 5.6 Performance Analytics

**URL:** `/archers/{id}/performance`

Click **View Full Analytics →** in the Performance Summary section to open the full analytics page.

Features:
- **Date range selector** — Last 7 Days, Last 30 Days, This Year, Last Year, or Custom date range
- **Stat cards** — Sessions, Best Score, Average Score, Hit Rate %
- **Score Trend chart** — Line chart showing score over time; competition sessions are highlighted in amber
- **Round Type Breakdown** — Bar chart comparing average scores across different round types
- **Competition vs Training** — Grouped bar chart comparing counts, averages, and bests
- **Sessions table** — Full list with date, round, score, X count, hits, and competition badge

---

## 6. Module: Coaches

### 6.1 Coach List

**URL:** `/coaches`
**Access:** Super Admin, Club Admin, Coach

Displays all coaches with: Ref No, Name, Email, Club, Coaching Level, State, and action buttons.

### 6.2 Adding a Coach

**Access:** Super Admin, Club Admin only

1. Click **+ ADD COACH**
2. Fill in:
   - **Full Name**, **Email** (required)
   - **Date of Birth**, **Gender**, **Phone**
   - **Coaching Level** — None / Kursus Asas Kejurulatihan / Level 1 / Level 2 / Level 3
   - **Club**, **Team**, **Address**, **State**, **Country**
   - **Photo**, **Notes**
3. Click **SAVE COACH**

Auto-generates a reference number (e.g. `COACH-00001`).

### 6.3 Coach Profile

**URL:** `/coaches/{id}`

Displays coach details, their assigned archers list, and training session history.

### 6.4 Assigning Archers to a Coach

1. Open the coach profile
2. Click **Archers** button (or navigate to `/coaches/{id}/archers`)
3. Select an archer from the dropdown and click **Assign**
4. To remove an archer, click **Remove** next to their name

### 6.5 Training Sessions

Coaches can log group training sessions for their assigned archers.

**URL:** `/coaches/{id}/training`

1. Click **+ New Training Session**
2. Fill in: Date, Title, Location, Notes, and select participating archers
3. Click **Save**

Training sessions are separate from individual scoring sessions and are used to track group attendance and notes.

---

## 7. Module: Sessions & Scorecards

### 7.1 Starting a New Session

**Access:** All roles (archers can only start sessions for themselves)

1. Go to an archer's profile and click **NEW SESSION**, or go to `/archers/{id}/sessions/create`
2. Select a **Round Type** from the category tabs (Indoor / Outdoor / Field / 3D / Clout)
3. Optionally override **Distance (m)** and **Target Face (cm)**
4. Choose session type: **Training** or **Competition**
5. Enter a **Date**
6. Click **Start Session**

> The system pre-selects the most common round type based on the archer's primary division (e.g. Recurve → WA 70m Outdoor Recurve).

### 7.2 Round Types

The system includes the full WA round set:

| Category | Round Types |
|---|---|
| **Indoor** | WA 18m Indoor (Recurve/Barebow), WA 18m Indoor Compound, WA 25m Indoor |
| **Outdoor** | WA 70m, 60m, 50m (Recurve/Compound/Barebow), 30m, 90m |
| **Field** | WA Field Marked, WA Field Unmarked |
| **3D** | WA 3D Round |
| **Clout** | WA Clout (Men), WA Clout (Women/Junior) |

### 7.3 Entering Scores

The scorecard displays **6 arrows per end**. Ends are grouped into sets of 6 (displayed one set at a time).

**Entering a score:**
1. Click on an arrow input cell
2. Type the score value and press **Enter** or **Tab** to advance
3. The end total updates automatically

**Navigation:**
- Use **← Previous** and **Next →** buttons to move between sets of ends
- Progress bar shows current position

**Saving:**
- Click **SAVE** to save the current set
- Scores are saved to the database immediately

### 7.4 Scoring Systems

Different round types use different valid score values:

| System | Used For | Valid Values |
|---|---|---|
| **Standard** | Recurve, Barebow, Longbow | X, 10–1, M |
| **Compound** | Compound rounds | X, 10–6, M (1–5 not valid) |
| **Field** | WA Field rounds | X (=6 pts), 6–1, M |
| **3D** | WA 3D Round | 20, 17, 10, M |
| **Clout** | WA Clout | 5–1, M |

> Invalid values are rejected with a shake animation. The hint text at the bottom of the scorecard shows the valid values for the current round type.

### 7.5 Viewing Session History

**URL:** `/archers/{id}/sessions`

Displays all sessions in reverse chronological order with: Date, Round Type, Score, Competition badge, and a link to view the scorecard.

---

## 8. Module: Clubs

### 8.1 Creating a Club

**Two ways to create a club:**

**A. Self-registration** (Club Admin)
- Go to `/register`, select **Club**, enter a Club Name and personal details
- A club is automatically created and linked to the new account

**B. Admin creation** (Super Admin)
1. Go to **Clubs** in the sidebar
2. Click **+ CREATE CLUB**
3. Fill in: Club Name (required), Registration No., Founded Year, Description, Contact Email/Phone, Website, Location/City, Full Address, State
4. Upload a club logo (JPG/PNG/WebP, max 2MB)
5. Toggle **Active** status
6. Click **SAVE CLUB**

### 8.2 Club Profile & Edit

**URL:** `/clubs/{id}`

Displays:
- Club logo / initials avatar
- Registration number and founded year
- Active/Inactive status
- Contact information (email, phone, website)
- Location and address
- Quick stats: Archers count, Coaches count, Sessions this month

**Editing a club:**
1. Click the **EDIT** button on the club profile
2. Update any fields
3. Click **SAVE CHANGES**

> Club Admins can only edit their own club. Super Admins can edit any club.

### 8.3 Club Dashboard

**URL:** `/clubs/{id}/dashboard`

The club performance dashboard shows aggregated data for all archers in the club.

**Date range selector:** Last 7 Days / Last 30 Days / This Year / Last Year / Custom

**Stat cards:**
- Total Archers in club
- Active archers (sessions in selected period)
- Best Score (highest score in period across all archers)
- Club Average Score

**Charts:**
- **Score Trend** (line chart) — daily average score across all club archers
- **Top Archers Leaderboard** (horizontal bar) — top 8 archers by average score in period

**Archer Performance Table:**
- Photo, Name, Ref No
- Sessions count, Best Score, Average Score, Hit Rate %
- **View →** link to individual archer's full analytics

**Recent Sessions Table:**
- Date, Archer, Round Type, Score, Competition badge, View link

### 8.4 Managing Members

**URL:** `/clubs/{id}/members`

The members page has two tabs: **Archers** and **Coaches**.

Each tab shows:
- **Current Members** — table with photo, name, ref no, club, and **Remove from Club** button
- **Pending Invitations** — invitees who haven't responded yet, with a **Cancel** button
- **Invite** section — search and invite new members

**Removing a member:**
- Click **Remove from Club** next to any current member
- The archer/coach's club association is cleared immediately (no email required)

### 8.5 Membership Invitations

Inviting archers or coaches to join your club requires their approval via email.

**To invite a member:**
1. On the Members page, select the **Archers** or **Coaches** tab
2. In the **Invite** section, search for an archer/coach by name
3. Select them from the dropdown
4. If the person is already in another club, a transfer warning will appear
5. Click **Send Invitation**

**What happens next:**
- The system sends an email to the archer/coach's registered email address
- The email contains **Accept** and **Decline** buttons with a secure link (valid 7 days)
- Clicking **Accept** updates their club to your club automatically
- Clicking **Decline** marks the invitation as declined
- If no response within 7 days, the invitation expires

**Cancelling an invitation:**
- In the **Pending Invitations** section, click **Cancel** next to any pending invite

---

## 9. Module: Admin Settings

**URL:** `/admin/settings`
**Access:** Super Admin only

### Registration Control

The **Registration Control** card (at the top of the Settings page) lets you suspend or re-open public self-registration independently for each module.

| Module | Effect when Suspended |
|--------|----------------------|
| **Archer** | The "Archer" button on `/register` is grayed out and labelled "Suspended". New archer self-registration is blocked. |
| **Coach** | The "Coach" button on `/register` is grayed out and labelled "Suspended". New coach self-registration is blocked. |
| **Club** | The "Club" button on `/register` is grayed out and labelled "Suspended". New club self-registration is blocked. |

**To suspend registration for a module:**
1. Find the module row (Archer / Coach / Club)
2. The current status badge shows **Open** (green) or **Suspended** (red)
3. Click **Suspend** — confirm the dialog — status changes to Suspended immediately

**To re-open registration:**
1. Click **Open Registration** on the suspended row
2. Confirm the dialog — status returns to Open

> Suspension only affects public self-registration at `/register`. Admins can still create archers, coaches, and clubs manually through the system regardless of this setting.

---

### System Logo
- Upload a logo (JPG/PNG/WebP, max 2MB)
- The logo appears in the sidebar, login page, and registration page
- Click **Remove Logo** to revert to the default target icon

### Typography
- **Body Font** and **Heading Font** — select from available Google Fonts
- **Heading Size** — adjust in px

### Login Page Typography
- Separate font/size controls for the login page

### Footer Text
- Customise the footer text shown across all pages

Click **SAVE SETTINGS** to apply changes.

---

## 10. Role Permissions Summary

| Action | Super Admin | Club Admin | Coach | Archer |
|--------|:-----------:|:----------:|:-----:|:------:|
| View all archers | ✅ | ✅ | ✅ | ❌ |
| Create archer | ✅ | ✅ | ❌ | ❌ |
| Edit any archer | ✅ | ✅ | ❌ | ❌ |
| Edit own archer profile | — | — | — | ✅ |
| Delete archer | ✅ | ❌ | ❌ | ❌ |
| View all coaches | ✅ | ✅ | ✅ | ❌ |
| Create coach | ✅ | ✅ | ❌ | ❌ |
| Edit any coach | ✅ | ✅ | ❌ | ❌ |
| Edit own coach profile | — | — | ✅ | — |
| Delete coach | ✅ | ❌ | ❌ | ❌ |
| Create session (any archer) | ✅ | ✅ | ✅ | ❌ |
| Create session (own) | — | — | — | ✅ |
| View scorecards | ✅ | ✅ | ✅ | ✅ (own) |
| View clubs list | ✅ | ❌ | ❌ | ❌ |
| Create / delete club | ✅ | ❌ | ❌ | ❌ |
| Edit own club | ✅ | ✅ | ❌ | ❌ |
| View club dashboard | ✅ | ✅ (own) | ❌ | ❌ |
| Invite/remove members | ✅ | ✅ (own) | ❌ | ❌ |
| Add/remove achievements | ✅ | ✅ | ❌ | ✅ (own) |
| Admin settings | ✅ | ❌ | ❌ | ❌ |

---

## 11. Frequently Asked Questions

**Q: I registered but the role button I need is grayed out.**
A: The administrator has suspended registration for that account type. Contact your system administrator to have it re-opened, or ask them to create your account manually.

**Q: I'm an archer and I can't see the Archers list.**
A: Archers cannot browse the full archer list. When you log in, you are taken directly to your own profile page. You can only view and edit your own information.

**Q: How do I assign a coach to an archer?**
A: Go to the coach's profile → click the **Archers** button → use the dropdown to select and assign archers to that coach.

**Q: The archer's default round type is wrong when creating a session.**
A: The system pre-selects based on the archer's first division (Recurve → WA 70m, Compound → WA 50m Compound, Barebow → WA 50m Barebow). If the archer has no division set, no default is applied. You can always select any round type manually.

**Q: I entered a score but it was rejected.**
A: Each round type has a specific scoring system. For example, Compound rounds do not accept scores of 1–5 (only 6–10, X, or M). The valid values are displayed at the bottom of the scorecard. Check the "Scoring Systems" section in this manual.

**Q: Can an archer be in multiple clubs?**
A: No. Each archer belongs to at most one club at a time. If you invite an archer who is already in another club, a transfer warning is shown. They must accept the invitation for their club to change.

**Q: An invitation I sent has expired. What do I do?**
A: Cancel the expired invitation on the Members page and send a new one. Invitations expire after 7 days.

**Q: I'm a Club Admin but I get a 403 error when accessing another club's dashboard.**
A: Club Admins can only access their own club's pages. Attempting to access another club's URL will return a 403 Forbidden error.

**Q: How do I delete a club?**
A: Only a Super Admin can delete a club. Go to the Clubs list, find the club, and use the Delete action. This action is irreversible.

**Q: How do I change my password?**
A: Log out, go to the login page, and click "Forgot password?" to reset your password via email. There is no in-app password change form at this time.

---

*For technical support, contact your system administrator.*
