let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const path_css = "css/";
const path_js = "js/";
const path_select2 = "public/select2/";
const path_moment = "public/moment/";
const path_unminified_css = "public/unminified_css/";
const path_bootstrap_datetimepicker = "public/bootstrap-datetimepicker/";
const path_public_css = "public/css/";
const path_public_js = "public/js/";

const css_add_attendance = "add_attendance.css";
const css_admin_timetables = "admin-timetables.css";
const css_classes = "classes.css";
const css_font_awesome = "font-awesome.min.css";
const css_medical_leave = "medical_leave.css";
const css_migration_tool = "migration-tool.css";
const css_students = "students.css";
const css_subjects = "subjects.css";
const css_teachers = "teachers.css";
const css_years = "years.css";
const css_admins = "admins.css";
const css_select2 = "select2.min.css";
const css_bootstrap_datetimepicker = "bootstrap-datetimepicker.min.css";

const js_select2 = "select2.min.js";
const js_moment = "moment.min.js";
const js_bootstrap_datetimepicker = "bootstrap-datetimepicker.js";

const js_medical_leave = "medical_leave.js";
const js_migration_tool = "migration_tool.js";
const js_students = "students.js";

const path_css_select2 = path_select2 + path_css + css_select2;
const path_css_bootstrap_datetimepicker = path_bootstrap_datetimepicker + path_css + css_bootstrap_datetimepicker;
const path_css_font_awesome = path_unminified_css + css_font_awesome;

const path_css_medical_leave = path_unminified_css + css_medical_leave;
const path_css_migration_tool = path_unminified_css + css_migration_tool;
const path_css_students = path_unminified_css + css_students;

const path_js_select2 = path_select2 + path_js + js_select2;
const path_js_moment = path_moment + path_js + js_moment;
const path_js_bootstrap_datetimepicker = path_bootstrap_datetimepicker + path_js + js_bootstrap_datetimepicker;

const output_css_medical_leave = path_public_css + css_medical_leave;
const output_css_migration_tool = path_public_css + css_migration_tool;
const output_css_students = path_public_css + css_students;

const output_js_medical_leave = path_public_js + js_medical_leave;
const output_js_migration_tool = path_public_js + js_migration_tool;
const output_js_students = path_public_js + js_students;

// Migration Tool
const req_css_migration_tool = [path_css_migration_tool, path_css_select2];
const req_js_migration_tool = [path_js_select2];

mix.styles(req_css_migration_tool, output_css_migration_tool);
mix.scripts(req_js_migration_tool, output_js_migration_tool);

// Medical Leaves
const req_css_medical_leave = [path_css_medical_leave, path_css_select2, path_css_bootstrap_datetimepicker, path_css_font_awesome];
const req_js_medical_leave = [path_js_select2, path_js_moment, path_js_bootstrap_datetimepicker];

mix.styles(req_css_medical_leave, output_css_medical_leave);
mix.scripts(req_js_medical_leave, output_js_medical_leave);

// Students
const req_css_students = [path_css_students, path_select2];
const req_js_students = [path_js_select2];

mix.styles(req_css_students, output_css_students);
mix.scripts(req_js_students, output_js_students);

mix.styles([path_unminified_css + css_add_attendance], 'public/css/' + css_add_attendance);
mix.styles([path_unminified_css + css_admin_timetables], 'public/css/' + css_admin_timetables);
mix.styles([path_unminified_css + css_classes], 'public/css/' + css_classes);
mix.styles([path_unminified_css + css_font_awesome], 'public/css/' + css_font_awesome);
mix.styles([path_unminified_css + css_subjects], 'public/css/' + css_subjects);
mix.styles([path_unminified_css + css_teachers], 'public/css/' + css_teachers);
mix.styles([path_unminified_css + css_years], 'public/css/' + css_years);
mix.styles([path_unminified_css + css_admins], 'public/css/' + css_admins);
