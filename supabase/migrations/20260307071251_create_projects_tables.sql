/*
  # Create Projects and Project Images Tables

  ## New Tables
  
  ### `projects`
  - `id` (integer, primary key, auto-increment)
  - `title` (text, not null) - Project title
  - `description` (text, not null) - Brief project description
  - `details` (text) - Detailed project information
  - `image` (text, not null) - Cover image filename
  - `status` (text, not null) - Project status (COMPLETED, ONGOING, UPCOMING)
  - `location` (text) - Project location
  - `created_at` (timestamp) - Record creation timestamp

  ### `project_images`
  - `id` (integer, primary key, auto-increment)
  - `project_id` (integer, not null) - Foreign key to projects table
  - `image` (text, not null) - Image filename
  - `created_at` (timestamp) - Record creation timestamp

  ## Security
  - Enable RLS on both tables
  - Add policies for public read access (portfolio website)
  - Add policies for authenticated admin users to manage projects

  ## Notes
  - Projects can have multiple images in the gallery
  - Status values are restricted to: COMPLETED, ONGOING, UPCOMING
  - Images are stored as filenames referencing the uploads directory
*/

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
  id SERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  details TEXT,
  image TEXT NOT NULL,
  status TEXT NOT NULL CHECK (status IN ('COMPLETED', 'ONGOING', 'UPCOMING')),
  location TEXT,
  created_at TIMESTAMPTZ DEFAULT now()
);

-- Create project_images table
CREATE TABLE IF NOT EXISTS project_images (
  id SERIAL PRIMARY KEY,
  project_id INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
  image TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT now()
);

-- Enable Row Level Security
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;
ALTER TABLE project_images ENABLE ROW LEVEL SECURITY;

-- Create policies for public read access (portfolio website needs to display projects)
CREATE POLICY "Anyone can view projects"
  ON projects
  FOR SELECT
  USING (true);

CREATE POLICY "Anyone can view project images"
  ON project_images
  FOR SELECT
  USING (true);

-- Create policies for authenticated users (admin) to manage projects
CREATE POLICY "Authenticated users can insert projects"
  ON projects
  FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update projects"
  ON projects
  FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete projects"
  ON projects
  FOR DELETE
  TO authenticated
  USING (true);

-- Create policies for authenticated users (admin) to manage project images
CREATE POLICY "Authenticated users can insert project images"
  ON project_images
  FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update project images"
  ON project_images
  FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete project images"
  ON project_images
  FOR DELETE
  TO authenticated
  USING (true);

-- Create indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_projects_status ON projects(status);
CREATE INDEX IF NOT EXISTS idx_projects_created_at ON projects(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_project_images_project_id ON project_images(project_id);
