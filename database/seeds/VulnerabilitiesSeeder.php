<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vulnerability;
use App\Models\Severity;

class VulnerabilitiesSeeder extends Seeder
{
    const DATA = [
        [
            'code' => 'HTTP',
            'name' => 'The website is served over HTTP',
            'severity' => 'Medium',
            'cwe' => 'CWE-319', // Cleartext Transmission of Sensitive Information
            'description' => "The connection to the site is not encrypted.",
            'attack_details' => <<<EOT
            Attackers on the same network, will be able to capture and modify requests.
            EOT,
            'remediation' => <<<EOT
            The affected site should be secured utilising the latest and most
            secure encryption protocols.
            EOT,
        ], [
            'code' => 'EXPOSED_VCS',
            'name' => 'Exposed Version Control System',
            'severity' => 'Critical',
            'cwe' => 'CWE-538', // Insertion of Sensitive Information into Externally-Accessible File or Directory
            'description' => <<<EOT
            The web server on the remote host allows read access to a Git / Svn repository.
            This potential flaw can be used to access content from the web server that might otherwise be private.
            EOT,
            'remediation' => <<<EOT
            Restrict access to the directory or remove it.
            EOT,
        ], [
            'code' => 'COOKIES_HTTPONLY',
            'name' => 'Cookie without HttpOnly flag set',
            'severity' => 'Medium',
            'cwe' => 'CWE-1004', // Sensitive Cookie Without 'HttpOnly' Flag
            'description' => <<<EOT
            If the HttpOnly attribute is set on a cookie, then the cookie's value
            cannot be read or set by client-side JavaScript. This measure makes certain
            client-side attacks, such as cross-site scripting, slightly harder to
            exploit by preventing them from trivially capturing the cookie's value via
            an injected script.
            EOT,
            'remediation' => <<<EOT
            Ensure that the cookie has the `HttpOnly` flag set.
            EOT,
        ], [
            'code' => 'COOKIES_SECURE',
            'name' => 'Cookie without Secure flag set',
            'severity' => 'Medium',
            'cwe' => 'CWE-614', // Sensitive Cookie in HTTPS Session Without 'Secure' Attribute
            'description' => <<<EOT
            Secure flag prevents cookies from being sent in clear text (under HTTP).
            A man in the middle attack on the users network against a target website
            without the Secure flag will allow the attacker to retrieve the user\'s cookies.
            EOT,
            'remediation' => <<<EOT
            If the cookie contains sensitive information, then the server
            should ensure that the cookie has the `secure` flag set.
            EOT,
        ], [
            'code' => 'COOKIES_SAMESITE',
            'name' => 'Cookie without SameSite flag set',
            'severity' => 'Low',
            'cwe' => 'CWE-1275', // Sensitive Cookie with Improper SameSite Attribute
            'description' => <<<EOT
            A CSRF attack on the target without the Same-Site flag, will allow the
            attacker to perform authenticated actions, using a valid existing session
            and victim interaction.
            EOT,
            'remediation' => <<<EOT
            Web browsers default behaviour may differ when processing cookies in a cross-site context,
            making the final decision to send the cookie in this context unpredictable.
            The SameSite attribute should be set in every cookie to enforce the expected result
            by developers. When using the 'None' attribute value, ensure that the cookie is also
            set with the `Secure` flag.
            EOT,
        ], [
            'code' => 'SESSION_FIXATION',
            'name' => 'Session Fixation',
            'severity' => 'High',
            'cwe' => 'CWE-384', // Session Fixation
            'description' => <<<EOT
            The cookies used for authentication the user after login in are still valid after the user logged out.
            Authenticating a user, or otherwise establishing a new user session, without invalidating any existing
            session identifier gives an attacker the opportunity to steal authenticated sessions.
            EOT,
            'remediation' => <<<EOT
            Invalidate the session identifiers once the user logged out.
            EOT,
        ], [
            'code' => 'PRIVATE_KEY_WORLD_READABLE',
            'name' => 'Private key files are world readable',
            'severity' => 'High',
            'cwe' => 'CWE-732', // Incorrect Permission Assignment for Critical Resource
            'description' => <<<EOT
            The private key files are readable by any user in the server.
            EOT,
            'remediation' => <<<EOT
            Private keys should only be readable by authorized users.
            EOT,
        ], [
            'code' => 'SSL_ISSUES',
            'name' => 'SSL Issues',
            'description' => <<<EOT
            EOT,
            'remediation' => <<<EOT
            EOT,
        ], [
            'code' => 'VULNERABLE_CODE',
            'name' => 'Vulnerable Code',
            'description' => <<<EOT
            EOT,
            'remediation' => <<<EOT
            EOT,
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::DATA as $item) {
            $this->seed($item);
        }
    }

    private function seed(array $data)
    {
        $definition = Vulnerability::firstOrCreate([
            'code' => $data['code'],
            'name' => $data['name'],
        ]);

        $definition->fill([
            'description' => isset($data['description']) ? $data['description'] : null,
            'attack_details' => isset($data['attack_details']) ? $data['attack_details'] : null,
            'remediation' => isset($data['remediation']) ? $data['remediation'] : null,
            'cwe' => isset($data['cwe']) ? $data['cwe'] : null,
        ]);

        if (isset($data['severity'])) {
            $definition->severity()->associate(Severity::where('name', $data['severity'])->first());
        }

        $definition->save();
    }
}
