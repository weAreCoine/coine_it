import DevLabel from '@/components/devLabel';
import TeamSection from '@/components/sections/teamSection';
import type { TeamMember } from '@/types/dto/sections';

type HealthCheckTeamProps = {
    members: TeamMember[];
};

export default function HealthCheckTeam({ members }: HealthCheckTeamProps) {
    return (
        <section id="health-check-team" aria-labelledby="healthCheckTeamLabel" className="relative">
            <DevLabel name="HealthCheckTeam" />
            <TeamSection
                kicker="Il team"
                title="Luca e Silvia"
                titleId="healthCheckTeamLabel"
                subtitle="Due professionisti con oltre dieci anni di esperienza nel digitale. Ogni progetto è seguito direttamente da entrambi."
                members={members}
            />
        </section>
    );
}
