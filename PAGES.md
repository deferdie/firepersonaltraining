# Page Documentation

This document contains detailed specifications for each page in the application. Each section can be copied and used as a prompt for building or modifying pages.

---

## Table of Contents

### Trainer Dashboard Pages
1. [Dashboard (Home)](#dashboard-home)
2. [Clients List](#clients-list)
3. [Client Profile](#client-profile)
4. [Library Overview](#library-overview)
5. [Programs Library](#programs-library)
6. [Workouts Library](#workouts-library)
7. [Exercises Library](#exercises-library)
8. [Meal Plans Library](#meal-plans-library)
9. [Articles Library](#articles-library)
10. [Messages](#messages)
11. [Calendar](#calendar)
12. [Settings](#settings)

### Client Dashboard Pages
13. [Client Dashboard Home](#client-dashboard-home)
14. [Today's Workout](#todays-workout)
15. [Client Progress](#client-progress)
16. [Client Messages](#client-messages)

### Marketing Pages
17. [Marketing Homepage](#marketing-homepage)
18. [Features Page](#features-page)
19. [Pricing Page](#pricing-page)

---

## Trainer Dashboard Pages

### Dashboard (Home)
**Route**: `/` or `/dashboard`

**Purpose**: Trainer's main dashboard showing an overview of their business and clients.

**Layout Style**:
- Clean, professional SaaS dashboard
- Light grey background (#f8f9fa)
- White cards with subtle shadows
- Grid-based layout with responsive columns

**Tone**: Professional, data-focused, actionable

**Features**:
- **Welcome Header**: Personalized greeting with trainer name
- **Key Metrics Cards** (4-column grid):
  - Total Active Clients (number)
  - This Week's Sessions (completed/scheduled)
  - Client Adherence Rate (percentage)
  - Monthly Revenue (dollar amount)
- **Quick Actions Section**:
  - Add New Client button (primary)
  - Create Program button
  - Schedule Session button
  - Send Message button
- **Today's Schedule**:
  - List of today's scheduled sessions
  - Client name, time, workout/session type
  - Quick actions: Start, Reschedule, Cancel
  - Empty state if no sessions
- **Recent Activity Feed**:
  - Client workout completions
  - New messages
  - Goal achievements
  - Payment received
  - Timestamped with client avatars
- **Clients Requiring Attention**:
  - Clients with low adherence
  - Clients with missed workouts
  - Clients with upcoming payment issues
  - AI-generated insights and suggested actions
- **Quick Stats Visualization**:
  - Weekly workout completions chart (bar/line chart)
  - Client growth over time
  - Revenue trend

**Components**:
- Stat cards with icons
- Calendar preview
- Activity timeline
- Data visualizations (recharts)
- Action buttons
- Client avatars

**Empty States**:
- No clients yet: "Get started by adding your first client"
- No sessions today: "No sessions scheduled for today"

---

### Clients List
**Route**: `/clients`

**Purpose**: View and manage all clients with filtering and search capabilities.

**Layout Style**:
- List/grid toggle view
- Search bar at top
- Filter sidebar or top bar
- White background for list items
- Hover states on cards

**Tone**: Organized, efficient, scannable

**Features**:
- **Search Bar**:
  - Real-time search by name, email, or phone
  - Search icon
  - Clear button
- **Filter Options**:
  - Status: Active, Inactive, All
  - Program: Filter by assigned program
  - Adherence: High (>80%), Medium (50-80%), Low (<50%)
  - Join Date: This month, Last 3 months, All time
  - Sort by: Name, Join Date, Last Active, Adherence
- **View Toggle**:
  - Grid view (cards)
  - List view (table)
- **Client Cards/Rows** display:
  - Avatar with initials (colored)
  - Client name (bold)
  - Email and phone
  - Status badge (Active/Inactive)
  - Current program name
  - Key metrics: Streak, Adherence %, Workouts completed
  - Last active timestamp
  - Quick actions dropdown:
    - View Profile
    - Send Message
    - Login to Client Dashboard
    - Reset Password
    - Deactivate/Activate
    - Delete
- **Bulk Actions**:
  - Select multiple clients
  - Send group message
  - Assign same program
  - Export data
- **Add Client Button**:
  - Prominent primary button (top right)
  - Opens modal or navigates to form
- **Pagination/Infinite Scroll**:
  - Handle large client lists
  - Show count: "Showing 20 of 147 clients"

**Empty State**:
- No clients: Large icon, "Add your first client to get started", CTA button
- No search results: "No clients found matching 'search term'"

---

### Client Profile
**Route**: `/clients/:clientId`

**Purpose**: Comprehensive view of a single client with all their data and interactions.

**Layout Style**:
- Full-width container
- Sticky header with client info
- Tab-based navigation
- Each section is a card or group of cards
- Lots of white space for readability

**Tone**: Detailed, organized, actionable

**Header Section**:
- Back button to clients list
- Large avatar with initials
- Client name (large, bold)
- Status badge (Active/Inactive)
- Email and phone
- Last active timestamp
- Action buttons:
  - Message
  - Login to Client Dashboard
  - Reset Password
  - Delete
- Key metrics bar (4 metrics):
  - Streak (days)
  - Workouts Completed/Total
  - Adherence %
  - Avg Session Duration
- AI Insight card (if applicable):
  - Most important insight
  - Suggested action button

**Tab Navigation**:
- Activity
- Goals
- Schedule
- Content
- Food
- Payments
- Notes
- Photos

#### Activity Tab
- **Workout History**:
  - Timeline view of completed workouts
  - Date, workout name, duration
  - Completion rate visualization
  - Filters: Date range, workout type
- **Streak Calendar**:
  - Visual calendar showing workout days
  - Current streak highlighted
  - Best streak stat
- **Performance Trends**:
  - Volume over time
  - Average duration
  - Completion rate trend

#### Goals Tab
- **Active Goals**:
  - Goal cards showing progress
  - Progress bars
  - Target date
  - Notes
- **Completed Goals**:
  - Archived goals with completion date
- **Add Goal Button**:
  - Opens modal to create new goal

#### Schedule Tab
- **Calendar View**:
  - Monthly calendar
  - Shows scheduled workouts/sessions
  - Click to view/edit
- **Upcoming Sessions**:
  - List view of next 7 days
  - Time, workout name
  - Quick actions

#### Content Tab
- **Assigned Programs**:
  - Current program card
  - Progress through program
  - Start date, expected end date
- **Assigned Workouts**:
  - Individual workouts assigned
  - Completion status
- **Assigned Articles**:
  - Educational content
  - Read/unread status
- **Assign Content Button**:
  - Opens modal to assign new content

#### Food Tab
- **Food Integrations Card**:
  - MyFitnessPal connection status
  - Cronometer connection status
  - Lose It! connection status
  - Nutritionix connection status
  - Connect/disconnect buttons
- **Daily Nutrition Summary**:
  - Calories, Protein, Carbs, Fat
  - Progress bars vs. targets
- **Food Journal**:
  - Entries grouped by date
  - Each entry shows: time, meal type, food name, macros
  - Add food entry button
  - Search/filter entries
- **Add Food Modal**:
  - Search food database
  - Quick add recent foods
  - Manual entry option
  - Macro inputs

#### Payments Tab
- **Subscription Info**:
  - Current plan
  - Amount per month
  - Next billing date
  - Payment method
- **Payment History Table**:
  - Date, amount, status, invoice link
  - Filters: Date range, status
- **Actions**:
  - Update payment method
  - Change subscription
  - Issue refund
  - Send invoice

#### Notes Tab
- **Notes List**:
  - Each note card with:
    - Category/title
    - Date created
    - Author (trainer name)
    - Content
    - Edit/delete actions
- **Add Note Button**:
  - Opens form to create note
  - Category dropdown
  - Rich text editor
- **Filter/Search Notes**:
  - By category
  - By date range
  - Search content

#### Photos Tab
- **Progress Photos Timeline**:
  - Chronological entries (newest first)
  - Each entry shows:
    - Date
    - Weight
    - Notes
    - Photo grid (Front, Side, Back angles)
- **Photo Entry Cards**:
  - Date and weight in header
  - Notes displayed
  - 3-column photo grid
  - Angle labels under each photo
  - Click photo to view fullscreen
  - Action menu: Add photos, Edit notes, Delete entry
- **Upload Photos Button**:
  - Opens modal to create new entry
  - Date picker
  - Weight input
  - Notes textarea
  - Photo upload (multiple)
  - Angle selection per photo
- **Lightbox Modal**:
  - Fullscreen photo viewing
  - Dark overlay
  - Close button
  - Navigate between photos (if multiple)
- **Progress Comparison** (future):
  - Side-by-side comparison tool
  - Select two dates to compare
- **Empty State**:
  - "No progress photos yet"
  - Helpful message about tracking visual progress
  - Upload button

---

### Library Overview
**Route**: `/library`

**Purpose**: Central hub for all trainer's content (programs, workouts, exercises, meal plans, articles).

**Layout Style**:
- Dashboard-style with large category cards
- Icon-based navigation
- Statistics for each category
- Grid layout

**Tone**: Organized, creative, builder-focused

**Features**:
- **Page Header**:
  - Title: "Content Library"
  - Subtitle: "Create and manage your programs, workouts, and content"
- **Category Cards** (2x3 grid):
  1. **Programs**:
     - Icon: Clipboard or Layers
     - Count: "12 Programs"
     - Description: "Multi-week training programs"
     - CTA: "View Programs" or "Create Program"
  2. **Workouts**:
     - Icon: Dumbbell
     - Count: "47 Workouts"
     - Description: "Individual workout templates"
     - CTA: "View Workouts" or "Create Workout"
  3. **Exercises**:
     - Icon: Activity
     - Count: "218 Exercises"
     - Description: "Exercise library with videos"
     - CTA: "View Exercises" or "Add Exercise"
  4. **Meal Plans**:
     - Icon: Utensils
     - Count: "8 Meal Plans"
     - Description: "Weekly nutrition plans"
     - CTA: "View Meal Plans" or "Create Plan"
  5. **Articles**:
     - Icon: BookOpen or FileText
     - Count: "23 Articles"
     - Description: "Educational content"
     - CTA: "View Articles" or "Write Article"
  6. **Templates** (future):
     - Icon: Copy
     - Description: "Pre-built templates"
     - CTA: "Browse Templates"
- **Recent Items**:
  - List of recently created/edited content
  - Content type, name, last modified
  - Quick actions
- **Quick Actions**:
  - Most common create actions
  - AI-assisted builder (future)

---

### Programs Library
**Route**: `/library/programs`

**Purpose**: Create and manage multi-week training programs.

**Layout Style**:
- List view with cards
- Sidebar or modal for creating/editing
- Preview panel option

**Tone**: Professional, structured, builder-focused

**Features**:
- **Search and Filter**:
  - Search by program name
  - Filter by: Duration, Goal, Difficulty, Status (Published/Draft)
  - Sort by: Name, Date created, Popularity
- **Program Cards**:
  - Program name (bold)
  - Duration: "8 weeks"
  - Goal: "Strength Building"
  - Difficulty: Beginner/Intermediate/Advanced
  - Thumbnail/cover image
  - Stats: Clients assigned, Completion rate
  - Preview button
  - Action menu:
    - Edit Program
    - Duplicate
    - Assign to Client
    - Delete
- **Create Program Button**:
  - Opens program builder
- **Program Builder** (modal or separate page):
  - Program name input
  - Description (rich text)
  - Duration (weeks)
  - Goal selection (dropdown or tags)
  - Difficulty level
  - Cover image upload
  - **Week-by-week builder**:
    - Add week
    - For each week:
      - Week label/name
      - Add workouts (search existing or create new)
      - Drag to reorder
      - Set workout days (Mon, Tue, etc.)
      - Rest days
  - Save as draft or publish
- **Program Preview**:
  - Full program overview
  - Week-by-week breakdown
  - Total workouts, exercises
  - Estimated time commitment

---

### Workouts Library
**Route**: `/library/workouts`

**Purpose**: Create and manage individual workout templates.

**Layout Style**:
- Grid or list view
- Quick preview on hover
- Filter sidebar

**Tone**: Energetic, detailed, practical

**Features**:
- **View Toggle**: Grid vs List
- **Search and Filter**:
  - Search by name
  - Filter by: Muscle Group, Equipment, Duration, Difficulty
  - Tags: Upper Body, Lower Body, Full Body, Cardio, HIIT, etc.
- **Workout Cards**:
  - Workout name
  - Thumbnail/preview
  - Duration estimate
  - Muscle groups (icons or tags)
  - Equipment needed (icons)
  - Exercise count
  - Action menu:
    - Edit
    - Duplicate
    - Assign to Client
    - Add to Program
    - Preview
    - Delete
- **Create Workout Button**:
  - Opens workout builder
- **Workout Builder**:
  - Workout name
  - Description
  - Tags/categories
  - Duration estimate
  - **Exercise Builder**:
    - Search exercise library
    - Add exercise
    - For each exercise:
      - Exercise name (with video/image preview)
      - Sets
      - Reps (or time)
      - Rest period (seconds)
      - Tempo (optional)
      - Notes/form cues
      - Drag to reorder
    - Support for supersets:
      - Group exercises together
      - Label as superset
    - Support for circuits:
      - Mark circuit start/end
      - Rounds
  - Save draft or publish
- **Workout Preview**:
  - Full workout view as client would see it
  - Exercise list with all details
  - Total estimated time
  - Equipment summary

---

### Exercises Library
**Route**: `/library/exercises`

**Purpose**: Maintain library of all exercises with videos/images and instructions.

**Layout Style**:
- Grid view with large thumbnails
- Detailed view modal or sidebar
- Categorized and searchable

**Tone**: Educational, instructional, visual

**Features**:
- **Search and Filter**:
  - Search by exercise name
  - Filter by:
    - Muscle group (Chest, Back, Legs, Shoulders, Arms, Core, etc.)
    - Equipment (Barbell, Dumbbell, Cables, Bodyweight, Machine, etc.)
    - Difficulty (Beginner, Intermediate, Advanced)
    - Movement type (Push, Pull, Hinge, Squat, Carry, etc.)
  - Tags
- **View Options**:
  - Grid with large thumbnails
  - List with small thumbnails
- **Exercise Cards**:
  - Exercise name
  - Thumbnail (video preview on hover)
  - Primary muscle group
  - Equipment needed (icons)
  - Quick actions:
    - View details
    - Add to workout
    - Edit
    - Delete
- **Add Exercise Button**:
  - Opens exercise form
- **Exercise Form**:
  - Exercise name
  - Description (rich text)
  - Video upload/embed (YouTube, Vimeo)
  - Image upload (multiple angles)
  - Primary muscle group
  - Secondary muscle groups
  - Equipment required
  - Difficulty level
  - Movement pattern
  - Form cues (bullet points)
  - Common mistakes
  - Variations
  - Tags
- **Exercise Detail View**:
  - Large video player or image carousel
  - Full description
  - All metadata displayed
  - Form cues clearly listed
  - Variations shown
  - Edit button
  - Used in: Shows which workouts/programs include this exercise

**Empty State**:
- "Start building your exercise library"
- Import from template library button

---

### Meal Plans Library
**Route**: `/library/meal-plans`

**Purpose**: Create weekly meal plans with full nutrition tracking.

**Layout Style**:
- List view with meal plan cards
- Builder interface with weekly calendar view
- Split view: days of week on left, meal details on right

**Tone**: Organized, nutritious, helpful

**Features**:
- **Search and Filter**:
  - Search by plan name
  - Filter by: Calories range, Diet type (Keto, Paleo, Vegan, etc.), Goal (Weight loss, Muscle gain, Maintenance)
  - Sort by: Name, Date created
- **Meal Plan Cards**:
  - Plan name
  - Description
  - Weekly calorie average
  - Diet type tags
  - Number of meals per day
  - Clients assigned
  - Action menu:
    - Edit
    - Duplicate
    - Assign to Client
    - Export PDF
    - Delete
- **Create Meal Plan Button**:
  - Opens meal plan builder
- **Meal Plan Builder**:
  - **Header**:
    - Meal plan name input
    - Description textarea
    - Diet type tags
    - Calorie target input
    - Macro targets (Protein %, Carbs %, Fat %)
  - **Weekly View**:
    - 7 columns (Monday - Sunday)
    - Each day shows:
      - Date/day label
      - Daily nutrition totals
      - Meals list
    - **Add Meal Button** per day:
      - Opens meal form
      - Meal type: Breakfast, Lunch, Dinner, Snack
      - Meal name/description
      - Add food items:
        - Search food database
        - Select food
        - Quantity/serving size
        - Nutrition auto-calculated
      - Manual food entry option
      - Recipe option (future)
    - **Meal Cards** show:
      - Meal type icon
      - Meal name
      - Food items list with portions
      - Nutrition: Calories, P/C/F
      - Edit/delete actions
  - **Nutrition Summary Panel**:
    - Daily averages
    - Weekly totals
    - Macro breakdown (pie chart)
    - Comparison to targets
  - **Copy Day Feature**:
    - Copy entire day's meals to another day
    - Useful for meal prep plans
  - Save draft or publish
- **Meal Plan Preview**:
  - Client-facing view
  - Full week layout
  - Shopping list generation (future)
  - Print/PDF export option

---

### Articles Library
**Route**: `/library/articles`

**Purpose**: Create and manage educational content for clients.

**Layout Style**:
- Blog-style list
- Rich preview cards
- Categories/tags sidebar

**Tone**: Educational, engaging, authoritative

**Features**:
- **Search and Filter**:
  - Search by title/content
  - Filter by: Category, Status (Published/Draft), Date
  - Categories: Nutrition, Training, Recovery, Mindset, Lifestyle, etc.
- **Article Cards**:
  - Featured image
  - Title
  - Excerpt/preview
  - Category tags
  - Published date
  - Read time estimate
  - Views/reads count
  - Action menu:
    - Edit
    - Duplicate
    - Assign to Client
    - Preview
    - Publish/Unpublish
    - Delete
- **Create Article Button**:
  - Opens article editor
- **Article Editor**:
  - Title input
  - Featured image upload
  - Category selection (multiple)
  - Tags input
  - **Rich Text Editor**:
    - Headings, bold, italic, lists
    - Images, videos, embeds
    - Code blocks (for workouts/recipes)
    - Links
    - Quotes
  - Excerpt/summary
  - SEO settings (future):
    - Meta description
    - Keywords
  - Save draft or publish
  - Schedule publishing (future)
- **Article Preview**:
  - How clients will see it
  - Clean reading layout
  - Related articles (future)

---

### Messages
**Route**: `/messages`

**Purpose**: Communication hub for trainer-client messaging.

**Layout Style**:
- Split view: Conversation list (left), Active chat (right)
- Mobile: Full-screen conversation, back to list
- Clean messaging interface similar to popular chat apps

**Tone**: Conversational, responsive, personal

**Features**:
- **Conversation List** (left panel):
  - Search conversations
  - Filter: All, Unread, Archived
  - Each conversation shows:
    - Client avatar
    - Client name
    - Last message preview
    - Timestamp
    - Unread badge (count)
    - Click to open
  - Sort by: Most recent, Unread first
- **Active Conversation** (right panel):
  - **Header**:
    - Client avatar and name
    - Client status (Active/Inactive)
    - Actions:
      - View Profile
      - Video Call (future)
      - Archive
      - More options
  - **Message Thread**:
    - Chronological messages
    - Date separators
    - Message bubbles:
      - Trainer messages (right, dark background)
      - Client messages (left, light background)
      - Timestamp
      - Read receipts (future)
    - Image/file attachments display
    - Emoji reactions (future)
  - **Input Area**:
    - Text input
    - Emoji picker
    - Attach file/image
    - Send button
    - "Client is typing..." indicator (future)
- **Quick Actions**:
  - Share workout
  - Share article
  - Schedule session
  - Send template message
- **Empty State**:
  - No conversations: "Start messaging your clients"
  - No message selected: "Select a conversation to start messaging"

---

### Calendar
**Route**: `/calendar`

**Purpose**: Manage all scheduled sessions, appointments, and client workouts.

**Layout Style**:
- Full calendar view (month/week/day options)
- Event cards with colors
- Modal for event details

**Tone**: Organized, time-management focused

**Features**:
- **View Switchers**:
  - Month view (default)
  - Week view
  - Day view
  - Agenda list view
- **Calendar Display**:
  - Color-coded events:
    - Client sessions (blue)
    - Personal time (grey)
    - Blocked time (red)
  - Events show: Time, client name, session type
  - Click event to view details
- **Event Details Modal**:
  - Client name (link to profile)
  - Session type
  - Date and time
  - Duration
  - Location/notes
  - Actions:
    - Edit
    - Reschedule
    - Cancel
    - Mark as complete
    - Send reminder
- **Create Event Button**:
  - Opens event form
  - Client selection (dropdown)
  - Session type
  - Date/time pickers
  - Duration
  - Recurring options
  - Notes
  - Send confirmation to client (checkbox)
- **Filters**:
  - By client
  - By session type
  - Show/hide completed
- **Today Indicator**:
  - Clearly marked current day
- **Navigation**:
  - Previous/Next buttons
  - Today button (jump to today)
  - Date picker for quick jump

---

### Settings
**Route**: `/settings`

**Purpose**: Trainer account settings, preferences, and business configuration.

**Layout Style**:
- Tabbed interface or sidebar navigation
- Form sections with clear labels
- Save buttons per section

**Tone**: Professional, administrative, clear

**Tabs/Sections**:

#### Profile
- Profile photo upload
- Full name
- Email (display only)
- Phone number
- Bio/about
- Certifications
- Website/social links
- Save button

#### Business
- Business name
- Business logo
- Business address
- Business phone
- Tax ID (optional)
- Save button

#### Branding
- Brand colors (color pickers)
- Logo upload
- Custom domain (future)
- Email templates customization
- Save button

#### Billing
- Current plan: "$10/month All-Inclusive"
- Payment method on file
- Billing history table
- Update payment method
- Cancel subscription (with confirmation)

#### Notifications
- Email notification preferences:
  - New client signup
  - Payment received
  - Client completes workout
  - Client sends message
  - System updates
- Push notification settings (future)
- Save button

#### Integrations
- Connected services:
  - MyFitnessPal
  - Cronometer
  - Lose It!
  - Nutritionix
  - Google Fit
  - Apple Health
  - Stripe
  - Zapier (future)
- Connect/disconnect buttons
- API keys display (if applicable)

#### Team (future)
- Add team members
- Role management
- Permissions

#### Security
- Change password
- Two-factor authentication (future)
- Active sessions
- Login history

---

## Client Dashboard Pages

### Client Dashboard Home
**Route**: `/client/dashboard`

**Purpose**: Client's main view - more mobile app-like, less corporate.

**Layout Style**:
- Mobile-first design
- Colorful, engaging
- Card-based layout
- Bottom navigation (mobile)
- Large, tappable elements

**Tone**: Motivating, friendly, encouraging, app-like

**Features**:
- **Welcome Header**:
  - Greeting: "Good morning, Sarah!"
  - Motivational message or quote
  - Profile avatar (top right)
- **Streak Card** (prominent):
  - Large streak number with fire icon
  - "You're on fire! 🔥"
  - Progress towards next milestone
  - Animated/colorful
- **Today's Workout Card**:
  - If workout scheduled:
    - Workout name
    - Duration estimate
    - Exercise count
    - "Start Workout" button (large, primary)
    - Preview exercises
  - If completed:
    - Checkmark and "Great job!"
    - Summary stats
  - If none:
    - Rest day message
    - "Enjoy your recovery"
- **Quick Stats Row**:
  - Workouts this week (progress ring)
  - Weekly calories (if tracking)
  - Total workouts completed
- **This Week's Plan**:
  - 7-day calendar
  - Each day shows:
    - Day of week
    - Workout type or rest day
    - Completion status (checkmark if done)
  - Swipeable on mobile
- **Recent Progress**:
  - Last workout completed with stats
  - Recent PR (personal record)
  - Weight/measurement update
- **Chat with Trainer**:
  - Last message preview
  - Unread badge
  - Quick tap to open chat
- **Content Feed**:
  - New articles assigned
  - Program updates
  - Trainer announcements
- **Bottom Navigation** (mobile):
  - Home (current)
  - Workouts
  - Progress
  - Messages
  - More/Profile

**Empty State**:
- No program assigned: "Your trainer will assign your program soon"
- First day: Onboarding tips and welcome message

---

### Today's Workout
**Route**: `/client/workout/:workoutId`

**Purpose**: Client performs and tracks their workout.

**Layout Style**:
- Full-screen on mobile
- Clean, distraction-free
- Large text and buttons
- Progress indicator at top

**Tone**: Motivating, clear, energizing

**Features**:
- **Header**:
  - Workout name
  - Back button
  - Progress bar (exercises completed)
- **Exercise Cards** (one at a time or scrollable list):
  - Exercise name (large)
  - Video/image
  - Sets/reps display
  - Rest timer between sets
  - Form cues (expandable)
  - **Set Tracking**:
    - Checkbox or button per set
    - Weight input (optional)
    - Reps input (if needed)
    - Mark complete
  - Next exercise preview
- **Rest Timer**:
  - Countdown display (large)
  - Sound/vibration when complete
  - Skip rest button
  - Add time button
- **Workout Controls**:
  - Pause workout
  - Finish early
  - Save progress
- **Completion Screen**:
  - "Workout Complete! 🎉"
  - Total time
  - Exercises completed
  - Share button (social, optional)
  - Leave note for trainer
  - Rate workout (stars or emoji)
- **Alternative Flow** (if they prefer):
  - See all exercises at once
  - Check them off as they go
  - Less hand-holding, more freedom

---

### Client Progress
**Route**: `/client/progress`

**Purpose**: Client views their progress, stats, and achievements.

**Layout Style**:
- Tabs or sections for different metrics
- Graphs and visualizations
- Photo gallery
- Achievement badges

**Tone**: Celebratory, motivating, data-driven

**Features**:
- **Tab Navigation**:
  - Overview
  - Workouts
  - Measurements
  - Photos
  - Goals
- **Overview Tab**:
  - Current streak (large)
  - Total workouts completed
  - Total hours trained
  - Workout completion rate
  - Monthly trend graph
  - Recent achievements/milestones
- **Workouts Tab**:
  - Workout history (list)
  - Filter by date range
  - Each entry: date, workout name, duration
  - Click for details
  - Personal records highlighted
  - Volume over time graph
- **Measurements Tab**:
  - Weight tracking:
    - Current weight
    - Starting weight
    - Change (+/- lbs)
    - Weight graph over time
    - Add new weight button
  - Body measurements (optional):
    - Chest, waist, hips, arms, legs
    - Add/update measurements
    - Graphs per measurement
- **Photos Tab**:
  - Progress photo timeline
  - Upload new photo button
  - Photo entry shows:
    - Date
    - Weight at time
    - Notes
    - Front/side/back photos
  - Compare photos feature:
    - Select two dates
    - Side-by-side view
    - Slider to overlay (future)
- **Goals Tab**:
  - Active goals with progress bars
  - Completed goals (with celebration)
  - Add new goal button (if trainer allows)

---

### Client Messages
**Route**: `/client/messages`

**Purpose**: Client communicates with their trainer.

**Layout Style**:
- Full-screen chat interface
- Mobile-first
- Similar to popular messaging apps

**Tone**: Friendly, conversational, supportive

**Features**:
- **Header**:
  - Trainer avatar and name
  - Online status (future)
  - Video call button (future)
- **Message Thread**:
  - Chronological messages
  - Date separators
  - Client messages (right, blue)
  - Trainer messages (left, grey)
  - Timestamps
  - Image/video attachments
  - Links to workouts/articles if shared
- **Input Area**:
  - Text input
  - Emoji picker
  - Camera/photo upload
  - Send button
- **Quick Actions** (optional):
  - Report issue
  - Request schedule change
  - Ask question templates

---

## Marketing Pages

### Marketing Homepage
**Route**: `/site`

**Purpose**: Public-facing homepage to attract new trainers.

**Layout Style**:
- Modern landing page
- Full-width sections
- Hero with CTA
- Features showcase
- Social proof
- Clean, premium feel

**Tone**: Professional, persuasive, benefit-focused

**Sections**:

#### Hero Section
- **Headline**: "The All-in-One Platform for Personal Trainers"
- **Subheadline**: "Manage clients, create programs, track progress, and grow your business - all for $10/month"
- **CTA Buttons**:
  - "Start Free Trial" (primary, large)
  - "Watch Demo" (secondary)
- **Hero Image/Video**:
  - Dashboard screenshot or demo video
  - Modern, professional
- **Trust Badges**:
  - "No credit card required"
  - "14-day free trial"
  - "Cancel anytime"

#### Features Overview
- **Section Title**: "Everything You Need to Succeed"
- **Feature Grid** (3-4 columns):
  1. **Client Management**:
     - Icon: Users
     - Short description
     - "Track workouts, progress, and communication"
  2. **Program Builder**:
     - Icon: Clipboard
     - "Create custom programs in minutes"
  3. **Meal Planning**:
     - Icon: Utensils
     - "Build nutrition plans with ease"
  4. **Progress Tracking**:
     - Icon: TrendingUp
     - "Photos, measurements, and analytics"
  5. **Messaging**:
     - Icon: MessageSquare
     - "Stay connected with clients"
  6. **Client App**:
     - Icon: Smartphone
     - "Beautiful mobile experience for clients"
- **CTA**: "See All Features" (links to features page)

#### Social Proof
- **Section Title**: "Loved by Personal Trainers"
- **Testimonial Cards** (3):
  - Trainer photo
  - Quote
  - Name, credentials, location
  - Star rating
- **Stats Row**:
  - "10,000+ trainers"
  - "500,000+ workouts created"
  - "4.9/5 stars"

#### Pricing Teaser
- **Section Title**: "Simple, Transparent Pricing"
- **Headline**: "$10/month. That's it."
- **Subheadline**: "Unlimited clients. All features included. No hidden fees."
- **Feature List**:
  - ✓ Unlimited clients
  - ✓ Unlimited programs & workouts
  - ✓ Unlimited storage
  - ✓ Mobile app for clients
  - ✓ Priority support
- **CTA**: "Start Free Trial" or "See Full Pricing"

#### How It Works
- **Section Title**: "Get Started in 3 Simple Steps"
- **Steps** (horizontal or vertical):
  1. Sign up (free for 14 days)
  2. Add clients & create programs
  3. Start training & growing
- **CTA**: "Get Started Now"

#### Integration Showcase
- **Section Title**: "Integrates with Your Favorite Tools"
- **Logo Grid**:
  - MyFitnessPal
  - Apple Watch
  - Google Fit
  - Stripe
  - Zapier
  - More...

#### Final CTA
- **Section Title**: "Ready to Transform Your Training Business?"
- **CTA Button**: "Start Free Trial Today"
- **Subtext**: "No credit card required. Cancel anytime."

#### Footer
- **Navigation Links**:
  - Features
  - Pricing
  - About
  - Blog
  - Contact
  - Terms of Service
  - Privacy Policy
- **Social Media Icons**
- **Copyright**

---

### Features Page
**Route**: `/site/features`

**Purpose**: Detailed breakdown of all platform features.

**Layout Style**:
- Long-form page
- Sections with images/screenshots
- Alternating layout (image left/right)
- Feature categories

**Tone**: Informative, comprehensive, benefit-focused

**Sections**:

#### Hero
- **Headline**: "Every Feature You Need, All in One Place"
- **Subheadline**: "From client management to program building, we've got you covered"
- **CTA**: "Start Free Trial"

#### Feature Categories (each with multiple features):

**1. Client Management**
- Client profiles
- Progress tracking
- Communication tools
- Schedule management
- Payment tracking
- Notes and observations
- Detailed description for each
- Screenshot/mockup

**2. Program & Workout Building**
- Program builder
- Workout templates
- Exercise library
- Video uploads
- Progression tracking
- Screenshot/mockup

**3. Nutrition & Meal Planning**
- Meal plan builder
- Food database
- Nutrition tracking
- Food app integrations
- Screenshot/mockup

**4. Client Experience**
- Mobile-app-like dashboard
- Workout tracking
- Progress photos
- Goal setting
- Messaging
- Screenshot/mockup

**5. Business Tools**
- Analytics & insights
- Payment processing
- Automated billing
- Client retention tools
- Screenshot/mockup

**6. Integrations**
- Food tracking apps
- Fitness wearables
- Payment processors
- Calendar sync
- Logo grid

#### Feature Comparison Table
- Compare plans (even though there's only one)
- Show all included features with checkmarks

#### Final CTA
- "Ready to Get Started?"
- CTA button
- Link to pricing

---

### Pricing Page
**Route**: `/site/pricing`

**Purpose**: Clear pricing information (simple since only one plan).

**Layout Style**:
- Centered, focused
- Large pricing card
- Feature list
- FAQ section
- Trust badges

**Tone**: Transparent, straightforward, no-nonsense

**Sections**:

#### Hero
- **Headline**: "Simple Pricing, Unlimited Possibilities"
- **Subheadline**: "One plan. All features. No surprises."

#### Pricing Card (centered, prominent)
- **Plan Name**: "All-Inclusive"
- **Price**: "$10" with "/month" smaller
- **Billing**: "Billed monthly. Cancel anytime."
- **CTA Button**: "Start Free Trial" (large)
- **Subtext**: "14 days free. No credit card required."
- **Feature List** (with checkmarks):
  - Unlimited clients
  - Unlimited programs & workouts
  - Unlimited meal plans
  - Unlimited storage
  - Client mobile experience
  - Progress tracking & photos
  - Messaging & communication
  - Food & fitness integrations
  - Payment processing
  - Priority support
  - All future features included

#### Trust Section
- **Money-back guarantee**
- **Secure payment** (Stripe logo)
- **No contracts**
- **Cancel anytime**

#### FAQ Section
- **Common questions**:
  - Is there really just one plan?
  - What happens after the free trial?
  - Can I cancel anytime?
  - Do you offer refunds?
  - Is there a setup fee?
  - How many clients can I have?
  - What payment methods do you accept?
  - Do my clients need to pay?

#### Comparison Section (optional)
- **"What you'd pay elsewhere"**:
  - Show competitor pricing
  - Highlight what's included for $10
  - Emphasize value

#### Final CTA
- **"Join Thousands of Trainers"**
- CTA button
- Testimonial quote or stat

---

## General Design Principles Across All Pages

### Consistency
- Use same card styling everywhere
- Consistent button styles
- Standard spacing (p-6 for cards, gap-4 or gap-6 between elements)
- Same navigation patterns

### Responsive Design
- Mobile-first approach
- Touch-friendly tap targets (min 44x44px)
- Responsive grids (grid-cols-1 md:grid-cols-2 lg:grid-cols-3)
- Hide/show elements appropriately

### Loading States
- Skeleton screens for cards
- Spinners for buttons
- Progress bars for operations

### Empty States
- Friendly illustrations or icons
- Clear message explaining why it's empty
- Actionable CTA to populate

### Error States
- Clear error messages
- Suggested actions to resolve
- Retry buttons

### Accessibility
- Semantic HTML
- ARIA labels
- Keyboard navigation
- Color contrast compliance
- Alt text for images

### Performance
- Lazy load images
- Code splitting for routes
- Optimize bundle size
- Fast page loads

---

_This document serves as a specification for building and maintaining pages in the application. Copy any section to use as a prompt for development._
